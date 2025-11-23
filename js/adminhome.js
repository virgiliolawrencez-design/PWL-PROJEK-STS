async function api(action, data = {}, method = "POST") {
  const form = new FormData();
  form.append("action", action);
  for (const k in data) form.append(k, data[k]);
  const res = await fetch(window.location.href, { method, body: form });
  return res.json();
}

async function refreshAll() {
  await loadOverview();
  await loadAccounts();
  await loadPCs();
}

async function loadOverview() {
  const accountsRes = await api("list_accounts");
  const pcsRes = await api("list_pcs");
  
  if (accountsRes.ok) {
    const accounts = accountsRes.accounts;
    document.getElementById("total-users").textContent = accounts.length;
    const totalCredits = accounts.reduce((sum, acc) => sum + parseFloat(acc.credit), 0);
    document.getElementById("total-credits").textContent = `Rp ${totalCredits.toLocaleString("id-ID")}`;
  }
  
  if (pcsRes.ok) {
    const pcs = pcsRes.pcs;
    document.getElementById("total-pcs").textContent = pcs.length;
    const activePCs = pcs.filter(pc => pc.status === "in_use").length;
    document.getElementById("active-pcs").textContent = activePCs;
    
    const grid = document.getElementById("pc-overview-grid");
    grid.innerHTML = "";
    pcs.forEach((pc) => {
      const div = document.createElement("div");
      div.className = `pc-overview-item ${pc.status}`;
      div.innerHTML = `
        <strong>${pc.label}</strong>
        <div class="status">${pc.status === "ready" ? "Ready" : "In Use"}</div>
      `;
      grid.appendChild(div);
    });
  }
}

async function loadPCs() {
  const res = await api("list_pcs");
  const grid = document.getElementById("pcGrid");
  grid.innerHTML = "";
  res.pcs.forEach((pc) => {
    const div = document.createElement("div");
    div.className = `pc-card ${pc.status}`;
    div.innerHTML = `
      <i class="fas fa-desktop"></i>
      <h4>${pc.label}</h4>
      <p>${pc.status === "ready" ? "Available" : `Used by ${pc.account_username || pc.account_name}`}</p>
      <div class="pc-actions">
        ${pc.status === "ready" 
          ? `<button class="btn-primary" onclick="startPC(${pc.id})">Start</button>`
          : `<button class="btn-danger" onclick="stopPC(${pc.id})">Stop</button>`
        }
      </div>
    `;
    grid.appendChild(div);
  });
}

async function loadAccounts() {
  const res = await api("list_accounts");
  const list = document.getElementById("accountsList");
  list.innerHTML = "";
  res.accounts.forEach((acc) => {
    const div = document.createElement("div");
    div.className = "user-card";
    div.innerHTML = `
      <h4>${acc.name}</h4>
      <p><strong>Username:</strong> ${acc.username}</p>
      <p><strong>Credit:</strong> Rp ${Number(acc.credit).toLocaleString("id-ID")}</p>
      <p><strong>Note:</strong> ${acc.note || "N/A"}</p>
      <div class="user-actions">
        <button class="btn-secondary" onclick="showEditAccount(${acc.id})">Edit</button>
        <button class="btn-danger" onclick="deleteAccount(${acc.id})">Delete</button>
      </div>
    `;
    list.appendChild(div);
  });
}

function filterAccounts() {
  const q = document.getElementById("searchAccount").value.toLowerCase();
  document.querySelectorAll(".user-card").forEach((card) => {
    card.style.display = card.innerText.toLowerCase().includes(q) ? "" : "none";
  });
}

function openModal(title, content, footer = '') {
  document.getElementById("modal-title").textContent = title;
  document.getElementById("modalContent").innerHTML = content;
  if (footer) {
    document.querySelector(".modal-footer").innerHTML = footer;
  }
  document.getElementById("modal").classList.add("active");
}

function closeModal() {
  document.getElementById("modal").classList.remove("active");
}

function showAddAccount() {
  const content = `
    <div class="form-group">
      <label for="a_name">Role</label>
      <select id="a_name">
        <option value="">Select role</option>
        <option value="Administrator">Administrator</option>
        <option value="Member">Member</option>
      </select>
    </div>
    <div class="form-group">
      <label for="a_username">Username</label>
      <input id="a_username" type="text" placeholder="Enter username">
    </div>
    <div class="form-group">
      <label for="a_password">Password</label>
      <input id="a_password" type="password" placeholder="Enter password">
    </div>
    <div class="form-group">
      <label for="a_credit">Initial Credit</label>
      <input id="a_credit" type="number" value="0" placeholder="Enter initial credit">
    </div>
    <div class="form-group">
      <label for="a_note">Note</label>
      <textarea id="a_note" placeholder="Enter note"></textarea>
    </div>
  `;
  const footer = `<button class="btn-secondary" onclick="closeModal()">Cancel</button>
                  <button class="btn-primary" onclick="doAddAccount()">Add User</button>`;
  openModal("Add New User", content, footer);
}

async function doAddAccount() {
  const res = await api("add_account", {
    name: document.getElementById("a_name").value,
    username: document.getElementById("a_username").value,
    password: document.getElementById("a_password").value,
    credit: document.getElementById("a_credit").value,
    note: document.getElementById("a_note").value,
  });
  if (!res.ok) return alert(res.error);
  closeModal();
  refreshAll();
}

async function showEditAccount(id) {
  const res = await api("list_accounts");
  const acc = res.accounts.find((a) => a.id == id);
  if (!acc) return;
  
  // Check if user is on a PC
  const pcsRes = await api("list_pcs");
  const userPC = pcsRes.pcs.find(pc => pc.current_account_id == id);
  
  const content = `
    <div class="form-group">
      <label for="e_name">Name</label>
      <input id="e_name" type="text" value="${acc.name}">
    </div>
    <div class="form-group">
      <label for="e_add_credit">Add Credit</label>
      <input id="e_add_credit" type="number" value="0" placeholder="Amount to add">
    </div>
    <div class="form-group">
      <label for="e_note">Note</label>
      <textarea id="e_note">${acc.note || ""}</textarea>
    </div>
    <div class="form-group">
      <label for="e_password">New Password (optional)</label>
      <input id="e_password" type="password" placeholder="Leave blank to keep current">
    </div>
    ${userPC ? `<div class="form-group">
      <button class="btn-danger" onclick="kickUser(${id})">Kick from PC (${userPC.label})</button>
    </div>` : ''}
  `;
  const footer = `<button class="btn-secondary" onclick="closeModal()">Cancel</button>
                  <button class="btn-primary" onclick="doEditAccount(${id})">Update User</button>`;
  openModal("Edit User", content, footer);
}

async function doEditAccount(id) {
  const res = await api("edit_account", {
    id,
    name: document.getElementById("e_name").value,
    add_credit: document.getElementById("e_add_credit").value,
    note: document.getElementById("e_note").value,
    password: document.getElementById("e_password").value,
  });
  if (!res.ok) return alert(res.error);
  closeModal();
  refreshAll();
}

async function kickUser(id) {
  if (!confirm("Kick this user from their PC?")) return;
  const res = await api("kick_user", { id });
  if (!res.ok) return alert(res.error);
  closeModal();
  refreshAll();
}

async function deleteAccount(id) {
  if (!confirm("Delete this user?")) return;
  const res = await api("delete_account", { id });
  if (!res.ok) return alert(res.error);
  refreshAll();
}

async function startPC(id) {
  const accId = prompt("Enter user ID to assign:");
  if (!accId) return;
  const res = await api("toggle_pc", {
    pc_id: id,
    mode: "start",
    account_id: accId,
  });
  if (!res.ok) alert(res.error);
  refreshAll();
}

async function stopPC(id) {
  const res = await api("toggle_pc", { pc_id: id, mode: "stop" });
  if (!res.ok) alert(res.error);
  refreshAll();
}

function showAddPc() {
  const content = `
    <div class="form-group">
      <label for="pc_label">PC Label</label>
      <input id="pc_label" type="text" placeholder="e.g., PC-13">
    </div>
  `;
  const footer = `<button class="btn-secondary" onclick="closeModal()">Cancel</button>
                  <button class="btn-primary" onclick="doAddPc()">Add PC</button>`;
  openModal("Add New PC", content, footer);
}

async function doAddPc() {
  const label = document.getElementById("pc_label").value;
  const res = await api("add_pc", { label });
  if (!res.ok) alert(res.error);
  closeModal();
  refreshAll();
}

// Navigation
document.querySelectorAll('.nav-item').forEach(item => {
  item.addEventListener('click', function() {
    document.querySelectorAll('.nav-item').forEach(nav => nav.classList.remove('active'));
    this.classList.add('active');
    
    const section = this.getAttribute('data-section');
    document.querySelectorAll('.section').forEach(sec => sec.classList.remove('active'));
    document.getElementById(section + '-section').classList.add('active');
    
    const titles = {
      overview: 'Dashboard Overview',
      users: 'User Management',
      pcs: 'PC Management'
    };
    document.getElementById('page-title').textContent = titles[section];
  });
});

// Initialize
refreshAll();
