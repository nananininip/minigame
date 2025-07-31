const wasteItems = [
  { name: "Banana", type: 'general', img: 'waste/assets/trash/banana.svg', question: 'เปลือกกล้วยนี้ควรทิ้งถังไหน?' },
  { name: 'Box', type: 'general', img: 'waste/assets/trash/box.svg', question: 'กล่องกระดาษนี้ควรทิ้งถังไหน?' },
  { name: 'Candy', type: 'general', img: 'waste/assets/trash/candy.svg', question: 'เปลือกลูกอมนี้ควรทิ้งถังไหน?' },
  { name: 'Coffee', type: 'general', img: 'waste/assets/trash/coffee.svg', question: 'แก้วกาแฟนี้ควรทิ้งถังไหน?' },
  { name: 'Cotton_blood', type: 'infected', img: 'waste/assets/trash/cotton_blood.svg', question: 'สำลีใช้แล้วที่มีเลือดนี้ควรทิ้งถังไหน?' },
  { name: 'Denture', type: 'infected', img: 'waste/assets/trash/denture.svg', question: 'ฟันปลอมนี้ควรทิ้งถังไหน?' },
  { name: 'Gloves_blood', type: 'infected', img: 'waste/assets/trash/glove_with_blood.svg', question: 'ถุงมือใช้แล้วที่มีเลือดนี้ควรทิ้งถังไหน?' },
  { name: 'Headcap_cloth', type: 'cloths', img: 'waste/assets/trash/head_cap_cloth.svg', question: 'หมวกคลุมผ่าตัดชนิดผ้านี้ควรทิ้งถังไหน?' },
  { name: 'Headcap_plastic', type: 'infected', img: 'waste/assets/trash/head_cap_no_recycle.svg', question: 'หมวกคลุมผ่าตัดใช้แล้วทิ้งนี้ควรทิ้งถังไหน?' },
  { name: "Headcap_recycle", type: 'recycle', img: 'waste/assets/trash/head_cap_recycle.svg', question: 'หมวกคลุมผ่าตัดที่รีไซเคิลได้นี้ควรทิ้งถังไหน?' },
  { name: 'Juice', type: 'general', img: 'waste/assets/trash/juice.svg', question: 'กล่องน้ำผลไม้นี้ควรทิ้งถังไหน?' },
  { name: 'Medical_mask', type: 'infected', img: 'waste/assets/trash/medical_mask.svg', question: 'หน้ากากอนามัยนี้ควรทิ้งถังไหน?' },
  { name: 'Surgical_gown_cloth', type: 'cloths', img: 'waste/assets/trash/surgical_gown_cloth.svg', question: 'เสื้อคลุมผ่าตัดแบบผ้านี้ควรทิ้งถังไหน?' },
  { name: "Surgical_gown_no_recycle", type: 'infected', img: 'waste/assets/trash/surgical_gown_no_recycle.svg', question: 'เสื้อคลุมผ่าตัดใช้แล้วทิ้งนี้ควรทิ้งถังไหน?' },
  { name: "Surgical_gown_recycle", type: 'recycle', img: 'waste/assets/trash/surgical_gown_recycle.svg', question: 'เสื้อคลุมผ่าตัดที่รีไซเคิลได้นี้ควรทิ้งถังไหน?' },
  { name: "T-drape", type: 'recycle', img: 'waste/assets/trash/t-drape.svg', question: 'ผ้าคลุมแบบ t-drape แบบรีไซเคิลได้นี้ควรทิ้งถังไหน?' },
];

const bins = [
  {
    type: 'general',
    label: 'ทั่วไป<br>ไม่ติดเชื้อ',
    lid: 'waste/assets/bin/green lid.svg',
    body: 'waste/assets/bin/green bin.svg'
  },
  {
    type: 'infected',
    label: 'ติดเชื้อ',
    lid: 'waste/assets/bin/red lid.svg',
    body: 'waste/assets/bin/red bin.svg'
  },
  {
    type: 'recycle',
    label: 'รีไซเคิลได้',
    lid: 'waste/assets/bin/red lid.svg',
    body: 'waste/assets/bin/red recycle.svg'
  },
  {
    type: 'cloths',
    label: 'ผ้าเชื้อ',
    // lid:  'waste/bin/laundry basket.svg',
    body: 'waste/assets/bin/laundry basket.svg'
  }
];

// const progressEl = document.querySelector('.progress');
const progressMask = document.getElementById('progressMask') || null;
const wasteCard = document.getElementById('wasteCard');
let current = 0, score = 0, correctCount = 0, maxTime = 15, timer = maxTime, timerInterval;
const scoreMultiplier = 20;

function getPlayerName() {
  return localStorage.getItem('playerName') || ('Guest' + Math.floor(Math.random() * 9000 + 1000));
}

function updateProgress(pct) {
  const filled = 100 - pct; // สีเทาขยับจากขวา
  if (progressMask) progressMask.style.width = filled + '%';
}

function handleWasteAnswer(selectedType) {
  const w = wasteItems[current];
  const isCorrect = selectedType === w.type;
  const resultEl = document.getElementById('wasteResult');

  document.querySelectorAll('.bin-drop').forEach(b => b.classList.remove("correct", "incorrect"));
  if (selectedType) {
    const selectedBinEl = document.querySelector(`.bin-drop[data-type="${selectedType}"]`);
    if (selectedBinEl) {
      selectedBinEl.classList.add(isCorrect ? 'correct' : 'incorrect');
    }
  }

  // เน้นถังที่ถูกต้อง (เสมอ)
  const correctBinEl = document.querySelector(`.bin-drop[data-type="${w.type}"]`);
  if (correctBinEl) correctBinEl.classList.add('correct');

  if (isCorrect) {
    correctCount++;
    const timeBonus = timer > 0 ? timer : 0;
    const gainedScore = timeBonus * scoreMultiplier;
    score += gainedScore;
    resultEl.innerHTML = `<span style="color:green; font-weight:bold;">✅ ถูกต้อง! +${gainedScore} คะแนน</span>`;
  }
  else {
    resultEl.innerHTML = `<span style="color:#e35656; font-weight:bold;">❌ ไม่ถูกต้อง</span>`;
  }

  current++;
  if (current < wasteItems.length) {
    setTimeout(() => {
      resultEl.innerHTML = '';
      showWaste(current);
    }, 1300);
  } else {
    showFinalScore();
  }
}

function launchConfetti() {
  // ปล่อย confetti 4 ชุดจากมุมต่าง ๆ
  confetti({
    particleCount: 100,
    spread: 70,
    origin: { y: 0.6 }
  });

  setTimeout(() => {
    confetti({
      particleCount: 150,
      spread: 100,
      origin: { x: 0.1, y: 0.3 }
    });
  }, 300);

  setTimeout(() => {
    confetti({
      particleCount: 150,
      spread: 100,
      origin: { x: 0.9, y: 0.3 }
    });
  }, 600);

  setTimeout(() => {
    confetti({
      particleCount: 200,
      spread: 120,
      origin: { y: 0.4 }
    });
  }, 900);
}


function showFinalScore() {
  wasteCard.innerHTML = `
    <div class="result" style="font-size:2.0rem; text-align:center; line-height:1.6;">
      🎉 คุณตอบถูกทั้งหมด <strong>${correctCount}</strong> ข้อ จาก ${wasteItems.length} ข้อ<br>
      🏆 คะแนนรวมทั้งหมด: <strong>${score}</strong> คะแนน
    </div>
  `;

  launchConfetti();

  const resultEl = document.getElementById('wasteResult');
  resultEl.innerHTML = '';
}


function shuffleArray(array) {
  for (let i = array.length - 1; i > 0; i--) {
    const j = Math.floor(Math.random() * (i + 1));
    [array[i], array[j]] = [array[j], array[i]];
  }
}

document.addEventListener('DOMContentLoaded', () => {
  shuffleArray(wasteItems);
  showWaste(0);
});


function showWaste(idx) {
  clearInterval(timerInterval);
  timer = maxTime;
  updateProgress(100);

  const w = wasteItems[idx];
  wasteCard.innerHTML = `
  <div class="waste-content">
    <div class="question">${w.question}</div>

    <div class="timer" style="font-size:3rem; font-weight:bold; margin-bottom:1.5rem;">
      ⏰ <span id="timerNum">${String(timer).padStart(2, '0')}</span> วินาที
    </div>

    <div class="draggable-item" draggable="true">
      <img src="${w.img}" alt="${w.name}" />
      <div class="drag-instruction">ลากมาวางที่ถังขยะ</div>
    </div>

    <div class="bins-row"></div>
    <div id="wasteResult" class="result"></div>
  </div>
`;



  const binsRow = document.querySelector('.bins-row');
  binsRow.innerHTML = '';
  bins.forEach(bin => {
    const hasLid = bin.lid !== undefined;

    binsRow.innerHTML += `
      <div class="bin-drop" data-type="${bin.type}" style="position:relative;">
        ${hasLid ? `<img class="bin-lid" src="${bin.lid}" />` : '<div style="height:40px;"></div>'}
        <img class="bin-body" src="${bin.body}" />
      </div>
    `;
  });

  // drag and drop setup
  const dragItem = wasteCard.querySelector('.draggable-item');
  dragItem.addEventListener('dragstart', e => { });

  document.querySelectorAll('.bin-drop').forEach(binEl => {
    binEl.ondragover = e => {
      e.preventDefault();
      binEl.classList.add('open', 'drag-over');
    };
    binEl.ondragleave = () => {
      binEl.classList.remove('open', 'drag-over');
    };
    binEl.ondrop = e => {
      e.preventDefault();
      document.querySelectorAll('.bin-drop').forEach(b => b.classList.remove('open', 'drag-over'));
      handleWasteAnswer(binEl.dataset.type);
    };
  });

  // Start countdown
  timerInterval = setInterval(() => {
    timer--;
    const timerEl = document.getElementById('timerNum');
    if (timerEl) timerEl.textContent = String(timer).padStart(2, '0');
    updateProgress((timer / maxTime) * 100);

    if (timer <= 0) {
      clearInterval(timerInterval);
      handleWasteAnswer(null);
    }
  }, 1000);
}

document.addEventListener('DOMContentLoaded', () => {
  showWaste(0);
});
