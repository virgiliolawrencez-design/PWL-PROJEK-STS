async function purchaseTime(package) {
    try {
        const response = await fetch('../main/billing.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=purchase_time&package=${package}`
        });
        const data = await response.json();
        if (data.success) {
            // Update credit and time displays
            document.getElementById('current-credit').textContent = new Intl.NumberFormat('id-ID').format(data.new_credit);
            document.getElementById('credit-display').textContent = new Intl.NumberFormat('id-ID').format(data.new_credit);
            // Restart timer with new session time
            if (window.timerInterval) clearInterval(window.timerInterval);
            startTimer(data.new_session_time);
            alert('Purchase successful!');
        } else {
            alert(data.message);
        }
    } catch (error) {
        console.error('Error purchasing time:', error);
        alert('An error occurred. Please try again.');
    }
}

async function loadBillingHistory() {
    try {
        const response = await fetch('../main/billing.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'action=get_history'
        });
        const history = await response.json();
        const historyDiv = document.getElementById('billing-history');
        if (history.length > 0) {
            let html = '<table><thead><tr><th>Package</th><th>Hours</th><th>Amount</th><th>Date</th></tr></thead><tbody>';
            history.forEach(item => {
                html += `<tr><td>${item.package_name}</td><td>${item.hours}</td><td>Rp ${new Intl.NumberFormat('id-ID').format(item.amount)}</td><td>${new Date(item.purchase_date).toLocaleString()}</td></tr>`;
            });
            html += '</tbody></table>';
            historyDiv.innerHTML = html;
        } else {
            historyDiv.innerHTML = '<p>No billing history found.</p>';
        }
    } catch (error) {
        console.error('Error loading billing history:', error);
        document.getElementById('billing-history').innerHTML = '<p>Error loading history.</p>';
    }
}

// Load session time and start decrementing timer
async function loadSessionTime() {
    try {
        const response = await fetch('../main/billing.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'action=get_session_time'
        });
        const data = await response.json();
        if (data.success) {
            startTimer(data.session_time);
        }
    } catch (error) {
        console.error('Error loading session time:', error);
    }
}

function updateTimerDisplay(timeRemaining) {
    const hours = Math.floor(timeRemaining / 3600);
    const minutes = Math.floor((timeRemaining % 3600) / 60);
    const seconds = timeRemaining % 60;
    const timerElement = document.getElementById('timer');
    const timeString = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
    if (timerElement) {
        timerElement.textContent = timeString;
    }
}

async function updateSessionTime(time) {
    // No longer needed as we use absolute timestamps
    // Timer syncs on page load and purchase
}

function startTimer(initialTime) {
    let timeRemaining = initialTime;
    updateTimerDisplay(timeRemaining);

    window.timerInterval = setInterval(async () => {
        timeRemaining--;
        updateTimerDisplay(timeRemaining);

        if (timeRemaining <= 0) {
            clearInterval(window.timerInterval);
            clearInterval(window.syncInterval);
            updateTimerDisplay(0);
            alert('Session time has expired!');
            window.location.href = '../main/member.php'; // Redirect to login
        }
    }, 1000);

    // Sync remaining time to DB every 10 seconds
    window.syncInterval = setInterval(async () => {
        if (timeRemaining > 0) {
            try {
                await fetch('../main/billing.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=update_session_time&remaining_time=${timeRemaining}`
                });
            } catch (error) {
                console.error('Error syncing session time:', error);
            }
        }
    }, 10000); // Every 10 seconds
}

// Load billing history and session time on page load
document.addEventListener('DOMContentLoaded', function() {
    loadBillingHistory();
    loadSessionTime();
});
