let quizCountdown;
const totalDuration = 50; // Total seconds
let timeLeft = sessionStorage.getItem("timeLeft")
  ? parseInt(sessionStorage.getItem("timeLeft"))
  : totalDuration;

const form = document.querySelector("form");
const timerBar = document.getElementById("timerBar");
const timeLeftDisplay = document.getElementById("timeLeft");

// Initialize UI
updateGlobalTimerUI(timeLeft, totalDuration);
startGlobalTimer(timeLeft);

function startGlobalTimer(startAt) {
  quizCountdown = setInterval(() => {
    startAt--;
    sessionStorage.setItem("timeLeft", startAt);
    updateGlobalTimerUI(startAt, totalDuration);

    if (startAt <= 0) {
      clearInterval(quizCountdown);
      sessionStorage.removeItem("timeLeft");
      autoSubmitFinal();
    }
  }, 1000);
}

function updateGlobalTimerUI(remaining, total) {
  timeLeftDisplay.textContent = remaining;
  const percent = (remaining / total) * 100;
  timerBar.style.width = percent + "%";

  // Dynamic Color Change based on time remaining
  if (percent <= 20) {
    timerBar.style.backgroundColor = "#dc3545"; // red
  } else if (percent <= 50) {
    timerBar.style.backgroundColor = "#ffc107"; // yellow
  } else {
    timerBar.style.backgroundColor = "#28a745"; // green
  }
}

function autoSubmitFinal() {
  // Check if answer is selected, if not set "No Answer"
  const selectedAnswer = document.getElementById("selectedAnswer");
  if (!selectedAnswer.value) {
    selectedAnswer.value = "No Answer";
  }

  // Disable buttons to prevent manual submission
  document.querySelectorAll(".answer-btn").forEach(btn => (btn.disabled = true));
  document.querySelector(".btn-submit").disabled = true;

  // Inform user clearly and submit automatically
  alert("⏰ หมดเวลา! ระบบจะส่งคำตอบให้อัตโนมัติค่ะ");
  form.submit();
}

// Prevent double submission
form.addEventListener("submit", () => {
  clearInterval(quizCountdown);
  sessionStorage.removeItem("timeLeft");
});
