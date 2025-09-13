document.addEventListener('DOMContentLoaded', function () {
    const loginForm = document.getElementById('loginForm');
    const errorMessage = document.getElementById('errorMessage');

    loginForm.addEventListener('submit', function (e) {
        e.preventDefault();

        const formData = new FormData(loginForm);

        fetch('member.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Redirect to memberhome.php on successful login
                window.location.href = 'memberhome.php';
            } else {
                // Show error message
                errorMessage.textContent = data.message;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            errorMessage.textContent = 'Terjadi kesalahan. Coba lagi.';
        });
    });
});
