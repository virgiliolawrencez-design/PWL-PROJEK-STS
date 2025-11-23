// Load session time and start decrementing timer
async function loadSessionTime() {
    try {
        const response = await fetch('../main/anno.php', {
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
                await fetch('../main/anno.php', {
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

// Load session time on page load
document.addEventListener('DOMContentLoaded', loadSessionTime);
