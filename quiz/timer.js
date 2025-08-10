;(function () {
  if (window.__quizTimerRAF) cancelAnimationFrame(window.__quizTimerRAF);
  window.__quizTimerRAF = null;

  var wrap = document.querySelector('.progress-bar-bg') || document.querySelector('.timer-wrap');
  if (!wrap) return;

  var total = parseInt(wrap.dataset.total || '0', 10);
  var remaining = parseInt(wrap.dataset.left || wrap.dataset.timeLeft || '0', 10);
  if (!isFinite(total) || total <= 0) total = 1;
  if (!isFinite(remaining) || remaining < 0) remaining = 0;

  var timeLeftEl = document.getElementById('timeLeft');
  var bar = document.getElementById('timerBar');
  var form = document.getElementById('quizForm') || document.querySelector('form');

  // Ensure hidden field for forced finish
  var forceInp = document.getElementById('force_finish');
  if (!forceInp && form) {
    forceInp = document.createElement('input');
    forceInp.type = 'hidden';
    forceInp.name = 'force_finish';
    forceInp.id = 'force_finish';
    forceInp.value = '0';
    form.appendChild(forceInp);
  }

  var submitted = false;
  function disableFormOnce() {
    if (!form) return;
    var btns = form.querySelectorAll('button, input[type="submit"]');
    btns.forEach(function (b) { b.disabled = true; });
  }
  function submitFinish() {
    if (submitted || !form) return;
    submitted = true;
    forceInp.value = '1';
    disableFormOnce();
    setTimeout(function () { form.submit(); }, 0);
  }

  var endTime = Date.now() + remaining * 1000;
  var lastWhole = remaining;

  function tick() {
    var now = Date.now();
    var ms = Math.max(0, endTime - now);
    var pct = Math.max(0, Math.min(100, (ms / (total * 1000)) * 100));
    if (bar) bar.style.width = pct + '%';

    var secs = Math.floor(ms / 1000);
    if (secs !== lastWhole) {
      lastWhole = secs;
      if (timeLeftEl) timeLeftEl.textContent = Math.max(0, secs);
    }

    if (ms <= 0) {
      if (bar) bar.style.width = '0%';
      if (timeLeftEl) timeLeftEl.textContent = '0';
      submitFinish();
      return;
    }
    window.__quizTimerRAF = requestAnimationFrame(tick);
  }

  if (bar) bar.style.width = (remaining / total * 100) + '%';
  if (timeLeftEl) timeLeftEl.textContent = remaining;
  window.__quizTimerRAF = requestAnimationFrame(tick);

  window.addEventListener('pageshow', function (e) {
    if (e.persisted) {
      var cur = parseInt(timeLeftEl && timeLeftEl.textContent, 10);
      if (isFinite(cur) && cur >= 0) {
        endTime = Date.now() + cur * 1000;
        lastWhole = cur + 1;
        if (bar) bar.style.width = (cur / total * 100) + '%';
      }
      if (window.__quizTimerRAF) cancelAnimationFrame(window.__quizTimerRAF);
      window.__quizTimerRAF = requestAnimationFrame(tick);
    }
  });

  if (form) {
    form.addEventListener('submit', function () {
      submitted = true;
      if (window.__quizTimerRAF) cancelAnimationFrame(window.__quizTimerRAF);
    });
  }
})();
