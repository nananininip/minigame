// Reset any waste-game session timers if needed
sessionStorage.removeItem("waste_timeLeft");

document.getElementById("startWasteBtn")?.addEventListener("click", () => {
  window.location.href = "instruction.html";
});
