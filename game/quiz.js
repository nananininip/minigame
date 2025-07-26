// quiz.js

// — Questions (add up to 4 or more) —
const questions = [
  {
    q: "แนวคิดเศรษฐกิจหมุนเวียน (Circular Economy) หมายถึงอะไร",
    options: [
      "ใช้ผลิตภัณฑ์ซ้ำให้คุ้มค่าที่สุดก่อนทำลายทิ้ง",
      "หมุนเวียนวัฏจักรอายุผลิตภัณฑ์ให้ใช้ได้นานที่สุด",
      "ผลิตเร็ว ใช้เร็ว ทิ้งเร็ว"
    ],
    answer: 1
  },
  // … เพิ่มคำถามอีก 3–4 ข้อที่นี่ …
];

// — State —
let currentQ   = 0;
let quizScore  = 0;
let quizTime   = 40;
let quizInterval;

// — Elements —
const progressEl     = document.querySelector('.progress');
const quizQuestionEl = document.getElementById('quizQuestion');
const quizOptionsEl  = document.getElementById('quizOptions');
const quizTimerEl    = document.getElementById('quizTimer');
const nextQuizBtn    = document.getElementById('nextQuiz');
const quizResult     = document.getElementById('quizResult');

// — Progress Helper —
function updateProgress(pct) {
  progressEl.style.width = pct + '%';
}

// — Load Question —
function loadQuiz() {
  const { q, options } = questions[currentQ];
  quizQuestionEl.textContent = q;
  quizOptionsEl.innerHTML = options
    .map((opt, i) => `<li data-idx="${i}">${opt}</li>`)
    .join('');
  document.querySelectorAll('.options li').forEach(li => {
    li.onclick = () => {
      const idx = +li.dataset.idx;
      if (idx === questions[currentQ].answer) {
        quizScore++;
        li.classList.add('correct');
      } else {
        li.classList.add('wrong');
      }
      document
        .querySelectorAll('.options li')
        .forEach(n => n.onclick = null);
    };
  });
  // reset timer & progress
  quizTime = 40;
  quizTimerEl.textContent = '00:40';
  updateProgress(0);
}

// — Timer —
function startQuizTimer() {
  quizInterval = setInterval(() => {
    quizTime--;
    quizTimerEl.textContent =
      (quizTime < 10 ? '00:0' : '00:') + quizTime;
    updateProgress(((40 - quizTime) / 40) * 100);
    if (quizTime <= 0) {
      clearInterval(quizInterval);
      quizResult.textContent = 'หมดเวลา!';
    }
  }, 1000);
}

// — Next Button —
nextQuizBtn.addEventListener('click', () => {
  clearInterval(quizInterval);
  if (currentQ < questions.length - 1) {
    currentQ++;
    loadQuiz();
    startQuizTimer();
  } else {
    quizResult.textContent = `คะแนนคุณ: ${quizScore}/${questions.length}`;
    nextQuizBtn.disabled = true;
  }
});

// — Init —
loadQuiz();
startQuizTimer();

