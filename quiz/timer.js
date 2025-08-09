// Smooth countdown for the quiz page
(function () {
  // Default quiz duration (seconds). Keep in sync with PHP $totalTime.
  var TOTAL = 50;

  var label = document.getElementById("timeLeft");
  var bar = document.getElementById("timerBar");
  if (!label || !bar) return;

  // Read remaining seconds rendered by PHP on page load
  var initial = parseInt(label.textContent, 10);
  if (!isFinite(initial) || initial < 0) initial = TOTAL;

  var endTime = Date.now() + initial * 1000;
  bar.style.transition = "width 100ms linear";

  function updateUI(now) {
    var remainingMs = Math.max(0, endTime - now);
    var remainingSec = Math.ceil(remainingMs / 1000);

    // Label
    label.textContent = remainingSec;

    // Progress bar (from full to 0)
    var pct = (remainingMs / (TOTAL * 1000)) * 100;
    if (pct < 0) pct = 0;
    if (pct > 100) pct = 100;
    bar.style.width = pct + "%";
  }

  function tick() {
    var now = Date.now();
    updateUI(now);

    if (now >= endTime) {
      // Time up → let server handle end state
      window.location.href = "quiz.php?timeout=1";
      return;
    }
    requestAnimationFrame(tick);
  }

  // Start
  // Set starting width based on initial seconds left
  bar.style.width = (initial / TOTAL) * 100 + "%";
  requestAnimationFrame(tick);

  // If page returns from bfcache, recompute timing
  window.addEventListener("pageshow", function (e) {
    if (e.persisted) {
      var current = parseInt(label.textContent, 10);
      if (isFinite(current) && current > 0) {
        endTime = Date.now() + current * 1000;
      }
      requestAnimationFrame(tick);
    }
  });
})();
