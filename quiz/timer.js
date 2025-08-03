let totalDuration = 50; // seconds
let timeLeft = totalDuration;
let quizCountdown;

// Restore time from previous session (if any)
if (sessionStorage.getItem("timeLeft")) {
    timeLeft = parseInt(sessionStorage.getItem("timeLeft"));
} else {
    sessionStorage.setItem("timeLeft", timeLeft);
}

// Update timer bar and label
function updateGlobalTimerUI(remaining, total) {
    document.getElementById("timeLeft").textContent = remaining;
    const percent = (remaining / total) * 100;
    const bar = document.getElementById("timerBar");
    bar.style.width = percent + "%";
    if (percent <= 20) {
        bar.style.background = "#f44336";
    } else if (percent <= 50) {
        bar.style.background = "#ffc107";
    } else {
        bar.style.background = "linear-gradient(90deg, #74c5e4 0%, #62d1a6 100%)";
    }
}

// Start timer on load
function startGlobalTimer(startAt) {
    updateGlobalTimerUI(startAt, totalDuration);

    quizCountdown = setInterval(() => {
        timeLeft--;
        sessionStorage.setItem("timeLeft", timeLeft);
        updateGlobalTimerUI(timeLeft, totalDuration);

        if (timeLeft <= 0) {
            clearInterval(quizCountdown);
            sessionStorage.removeItem("timeLeft");
            autoSubmitFinal();
        }
    }, 1000);
}

// When time runs out, redirect to result/exit page
function autoSubmitFinal() {
    window.location.href = "quiz.php?timeout=1";
}

window.onload = function() {
    startGlobalTimer(timeLeft);
}
