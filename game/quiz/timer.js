let quizCountdown;
let totalDuration = 50; // total seconds
let timeLeft = totalDuration;

// Load saved timeLeft from sessionStorage if available
if (sessionStorage.getItem("timeLeft")) {
    timeLeft = parseInt(sessionStorage.getItem("timeLeft"));
} else {
    sessionStorage.setItem("timeLeft", timeLeft);
}

startGlobalTimer(timeLeft);

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

function updateGlobalTimerUI(remaining, total) {
    document.getElementById("timeLeft").textContent = remaining;
    const percent = (remaining / total) * 100;
    const bar = document.getElementById("timerBar");
    bar.style.width = percent + "%";

    if (percent <= 30) {
        bar.style.backgroundColor = "#dc3545"; // red
    } else if (percent <= 60) {
        bar.style.backgroundColor = "#ffc107"; // yellow
    } else {
        bar.style.backgroundColor = "#28a745"; // green
    }
}

function autoSubmitFinal() {
    alert("หมดเวลา! ระบบจะส่งคำตอบให้อัตโนมัติ");
    document.querySelector("form").submit();
}
