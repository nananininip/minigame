const wasteItems = [
  { name: 'Bottle', type: 'recycle', img: 'waste/bottle.svg', question: 'ขวดน้ำพลาสติกนี้ควรทิ้งถังไหน?' },
  { name: 'Glove', type: 'infected', img: 'waste/glove.svg', question: 'ถุงมือนี้ควรทิ้งถังไหน?' },
  { name: 'Bandage', type: 'linen', img: 'waste/bandage.svg', question: 'ผ้าก๊อซใช้แล้วควรทิ้งถังไหน?' },
  { name: 'Newspaper', type: 'general', img: 'waste/newspaper.svg', question: 'กระดาษหนังสือพิมพ์นี้ควรทิ้งถังไหน?' }
];

const bins = [
  {
    type: 'general',
    label: 'ทั่วไป<br>ไม่ติดเชื้อ',
    lid:  'waste/green lid.svg',
    body: 'waste/green bin.svg'
  },
  {
    type: 'infected',
    label: 'ติดเชื้อ',
    lid:  'waste/red lid.svg',
    body: 'waste/red bin.svg'
  },
  {
    type: 'recycle',
    label: 'รีไซเคิลได้',
    lid:  'waste/red recycle.svg',
    body: 'waste/green bin.svg' // use your recycle bin svg here
  },
  {
    type: 'linen',
    label: 'ผ้าเชื้อ',
    lid:  'waste/laundry basket.svg',
    body: 'waste/laundry basket.svg'
  }
];

const progressEl = document.querySelector('.progress');
const wasteCard = document.getElementById('wasteCard');
let current = 0, score = 0, maxTime = 12, timer = maxTime, timerInterval;

function getPlayerName() {
  return localStorage.getItem('playerName') || ('Guest' + Math.floor(Math.random()*9000+1000));
}
function updateProgress(pct) { progressEl.style.width = pct + '%'; }

function showWaste(idx) {
  clearInterval(timerInterval);
  timer = maxTime;
  updateProgress(0);

  const w = wasteItems[idx];
  wasteCard.innerHTML = `
    <div class="question">${w.question}</div>
    <div class="timer" style="margin-bottom:1.2rem;">⏰ <span id="timerNum">${String(timer).padStart(2,'0')}</span> วินาที</div>
    <div class="draggable-item" draggable="true" style="margin: 0 auto 1.5rem auto; display: inline-block;">
      <img src="${w.img}" alt="${w.name}" style="width:72px; height:72px;">
      <div style="font-size:0.9rem; color:#555;">ลากมาวางที่ถังขยะ</div>
    </div>
    <div class="bins-row"></div>
  `;

  // Render bins
  const binsRow = document.querySelector('.bins-row');
  binsRow.innerHTML = '';
  bins.forEach(bin => {
    binsRow.innerHTML += `
      <div class="bin-drop" data-type="${bin.type}" style="position:relative;">
        <img class="bin-lid" src="${bin.lid}" />
        <img class="bin-body" src="${bin.body}" />
      </div>
    `;
  });

  // Setup drag/drop
  const dragItem = wasteCard.querySelector('.draggable-item');
  dragItem.addEventListener('dragstart', e => {});

  document.querySelectorAll('.bin-drop').forEach(binEl => {
    binEl.ondragover = e => {
      e.preventDefault();
      binEl.classList.add('open', 'drag-over');
    };
    binEl.ondragleave = e => {
      binEl.classList.remove('open', 'drag-over');
    };
    binEl.ondrop = e => {
      e.preventDefault();
      document.querySelectorAll('.bin-drop').forEach(b => b.classList.remove('open', 'drag-over'));
      handleWasteAnswer(binEl.dataset.type);
    };
  });

  // --- COUNTDOWN TIMER (reduces, never increases!) ---
  timerInterval = setInterval(() => {
    timer--;
    document.getElementById('timerNum').textContent = String(timer).padStart(2,'0');
    updateProgress(((maxTime-timer)/maxTime)*100);
    if (timer <= 0) {
      clearInterval(timerInterval);
      handleWasteAnswer(null);
    }
  }, 1000);
}

function handleWasteAnswer(selectedType) {
  clearInterval(timerInterval);
  const correctType = wasteItems[current].type;
  if (selectedType && selectedType === correctType) score++;
  document.querySelectorAll('.bin-drop').forEach(binEl => {
    if (binEl.dataset.type === correctType) binEl.classList.add('open');
    if (binEl.dataset.type === selectedType && selectedType !== correctType)
      binEl.classList.add('wrong');
  });
  setTimeout(() => nextWaste(), 1100);
}

function nextWaste() {
  current++;
  if (current < wasteItems.length) {
    showWaste(current);
  } else {
    finishGame();
  }
}

function finishGame() {
  const playerName = getPlayerName();
  const entry = {
    name: playerName, score, total: wasteItems.length,
    game: 'waste', date: Date.now()
  };
  let allScores = JSON.parse(localStorage.getItem('scoreboard') || '[]');
  allScores.push(entry); localStorage.setItem('scoreboard', JSON.stringify(allScores));

  wasteCard.innerHTML = `
    <div style="font-size:1.28rem;font-weight:600;">
      คุณได้คะแนน ${score} / ${wasteItems.length}
    </div>
    <div style="margin-top:2.2rem;">
      <button class="start-btn" onclick="window.location='quiz.html'">เล่นเกมควิซต่อ</button>
      <button class="start-btn" style="background:#f5a623;" onclick="window.location='scoreboard.html'">ดูตารางคะแนน</button>
    </div>
  `;
  updateProgress(100);
}

showWaste(0);
