sessionStorage.removeItem("timeLeft");

document.getElementById("startQuizBtn")?.addEventListener("click", () => {
  window.location.href = "quiz.php";
});
