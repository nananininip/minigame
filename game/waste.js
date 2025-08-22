let wasteStartTime = Date.now();

const wasteItems = [
  { name: "Banana", type: 'general',  img: '../assets/trash/banana.svg',                  question: 'เปลือกกล้วยควรทิ้งถังไหน?' },
  { name: 'Box', type: 'general',     img: '../assets/trash/box.svg',                     question: 'กล่องกระดาษควรทิ้งถังไหน?' },
  { name: 'Candy', type: 'general',   img: '../assets/trash/candy.svg',                   question: 'เปลือกลูกอมควรทิ้งถังไหน?' },
  { name: 'Coffee', type: 'general',  img: '../assets/trash/coffee.svg',                  question: 'แก้วกาแฟควรทิ้งถังไหน?' },
  { name: 'Cotton_blood', type: 'infected', img: '../assets/trash/cotton_blood.svg',     question: 'สำลีใช้แล้วควรทิ้งถังไหน?' },
  { name: 'Denture', type: 'infected',      img: '../assets/trash/denture.svg',          question: 'ฟันปลอมควรทิ้งถังไหน?' },
  { name: 'Gloves_blood', type: 'infected', img: '../assets/trash/glove_with_blood.svg', question: 'ถุงมือใช้แล้วควรทิ้งถังไหน?' },
  { name: 'Headcap_cloth', type: 'cloths',  img: '../assets/trash/head_cap_cloth.svg',   question: 'หมวกคลุมผ่าตัดชนิดผ้าควรทิ้งถังไหน?' },
  { name: 'Headcap_plastic', type: 'infected', img: '../assets/trash/head_cap_no_recycle.svg', question: 'หมวกคลุมผ่าตัดใช้แล้วทิ้งควรทิ้งถังไหน?' },
  { name: "Headcap_recycle", type: 'recycle', img: '../assets/trash/head_cap_recycle.svg',      question: 'หมวกคลุมผ่าตัดที่รีไซเคิลได้ควรทิ้งถังไหน?' },
  { name: 'Juice', type: 'general',   img: '../assets/trash/juice.svg',                  question: 'กล่องน้ำผลไม้ควรทิ้งถังไหน?' },
  { name: 'Medical_mask', type: 'infected', img: '../assets/trash/medical_mask.svg',     question: 'หน้ากากอนามัยควรทิ้งถังไหน?' },
  { name: 'Surgical_gown_cloth', type: 'cloths', img: '../assets/trash/surgical_gown_cloth.svg', question: 'เสื้อคลุมผ่าตัดแบบผ้าควรทิ้งถังไหน?' },
  { name: "Surgical_gown_no_recycle", type: 'infected', img: '../assets/trash/surgical_gown_no_recycle.svg', question: 'เสื้อคลุมผ่าตัดใช้แล้วทิ้งควรทิ้งถังไหน?' },
  { name: "Surgical_gown_recycle", type: 'recycle', img: '../assets/trash/surgical_gown_recycle.svg', question: 'เสื้อคลุมผ่าตัดที่รีไซเคิลได้ควรทิ้งถังไหน?' },
  { name: "T-drape", type: 'recycle', img: '../assets/trash/t-drape.svg',                question: 'ผ้าคลุม t-drape แบบรีไซเคิลได้นี้ควรทิ้งถังไหน?' },
];

const bins = [
  { type: 'general',  label: 'ทั่วไป<br>ไม่ติดเชื้อ', lid: '../assets/bin/green lid.svg', body: '../assets/bin/green bin.svg' },
  { type: 'infected', label: 'ติดเชื้อ',               lid: '../assets/bin/red lid.svg',   body: '../assets/bin/red bin.svg'   },
  { type: 'recycle',  label: 'รีไซเคิลได้',            lid: '../assets/bin/red lid.svg',   body: '../assets/bin/red recycle.svg' },
  { type: 'cloths',   label: 'ผ้าเชื้อ',               body: '../assets/bin/laundry basket.svg' }
];

const mascots = [
  { type: 'best',  label: 'GREEN GARDIAN',  name: 'ดอกเตอร์กรีน', minScore: 1200, svg: '../assets/mascots/green_gardian.svg',   description: 'ผู้พิทักษ์ของโรงพยาบาล<br>ช่วยโลกได้อย่างมือโปร!' },
  { type: 'good',  label: 'ECO EXPLORER',   name: 'น้องอีโค่',     minScore: 750,  svg: '../assets/mascots/eco_explorer.svg',    description: 'พยาบาลสายแยกขยะ<br>พยายามอีกนิด<br>เป็นนักแยกขั้นเทพแน่นอน!' },
  { type: 'mid',   label: 'WASTE WATCHER',  name: 'ผู้ช่วยคลีน',   minScore: 500,  svg: '../assets/mascots/waste_watcher.svg',   description: 'เจ้าหน้าที่จัดการขยะผู้ใจดี<br>เริ่มต้นได้ดีแล้ว<br>แต่ยังมีอะไรให้เรียนรู้อีกเพียบ!' },
  { type: 'rookie',label: 'ROOKIE RECYCLER',name: 'น้อง Cure',     minScore: 0,    svg: '../assets/mascots/rookie_recycler.svg', description: 'เด็กฝึกงานสายกรีน<br>เพิ่งเริ่มต้นแต่มีไฟ<br>มาเรียนรู้ไปด้วยกัน!' }
];

function getMascotByScore(score) {
  const sorted = mascots.slice().sort((a, b) => b.minScore - a.minScore);
  return sorted.find(m => score >= m.minScore) || sorted[sorted.length - 1];
}

/* ---------- สุ่มชุดคำถาม 10 ชิ้น ตามเงื่อนไข ---------- */
const ALWAYS_ALL_TYPES = new Set(['recycle','cloths']);      // ต้องเอามาทั้งหมด
const BALANCE_TYPES     = ['general','infected'];             // กระจายเท่า ๆ กัน
let roundItems = [];                                          // เด็คสำหรับรอบนี้

function shuffle(arr) {
  const a = arr.slice();
  for (let i = a.length - 1; i > 0; i--) {
    const j = Math.floor(Math.random() * (i + 1));
    [a[i], a[j]] = [a[j], a[i]];
  }
  return a;
}

function groupByType(items) {
  return items.reduce((acc, it) => {
    (acc[it.type] ||= []).push(it);
    return acc;
  }, {});
}

function buildRound(allItems, totalWant = 10) {
  const byType = groupByType(allItems);

  // 1) ต้องเอามาทั้งหมดของ recycle + cloths
  const mustShow = [];
  ALWAYS_ALL_TYPES.forEach(t => {
    if (byType[t]) mustShow.push(...byType[t]);
  });

  // ถ้าอยากได้รวมยังไงก็ต้องได้ไม่น้อยกว่าจำนวน mustShow
  const remainingSlots = Math.max(0, totalWant - mustShow.length);

  // 2) แบ่งโควต้าเท่า ๆ กันให้ general & infected
  const types = BALANCE_TYPES.filter(t => byType[t] && byType[t].length > 0);
  let base = types.length ? Math.floor(remainingSlots / types.length) : 0;
  let leftover = types.length ? remainingSlots % types.length : 0;

  // สุ่มลำดับใครจะได้ส่วนเกิน
  const typesOrder = shuffle(types);

  const picked = [];
  const poolsLeft = {};

  typesOrder.forEach((t, idx) => {
    const want = Math.min(byType[t].length, base + (idx < leftover ? 1 : 0));
    const shuffled = shuffle(byType[t]);
    picked.push(...shuffled.slice(0, want));
    // เก็บของเหลือไว้เติมถ้าขาด
    poolsLeft[t] = shuffled.slice(want);
  });

  // เผื่อกรณีมี type ไหนของไม่พอ ยังยังได้ไม่ครบ remainingSlots -> เติมจากกองที่เหลือรวม
  if (picked.length < remainingSlots) {
    const refillPool = shuffle(Object.values(poolsLeft).flat());
    picked.push(...refillPool.slice(0, remainingSlots - picked.length));
  }

  // รวม + สุ่มลำดับสุดท้าย
  const final = shuffle([...mustShow, ...picked].slice(0, totalWant));
  return final;
}

/* ---------- เกมหลัก ---------- */
const progressMask = document.getElementById('progressMask') || null;
const wasteCard = document.getElementById('wasteCard');
let current = 0, score = 0, correctCount = 0, maxTime = 15, timer = maxTime, timerInterval;
const scoreMultiplier = 10;

function updateProgress(pct) {
  const filled = 100 - pct;
  if (progressMask) progressMask.style.width = filled + '%';
}

function handleWasteAnswer(selectedType) {
  const w = roundItems[current];
  const isCorrect = selectedType === w.type;
  const resultEl = document.getElementById('wasteResult');

  document.querySelectorAll('.bin-drop').forEach(b => b.classList.remove("correct", "incorrect"));
  if (selectedType) {
    const selectedBinEl = document.querySelector(`.bin-drop[data-type="${selectedType}"]`);
    if (selectedBinEl) selectedBinEl.classList.add(isCorrect ? 'correct' : 'incorrect');
  }

  const correctBinEl = document.querySelector(`.bin-drop[data-type="${w.type}"]`);
  if (correctBinEl) correctBinEl.classList.add('correct');

  if (isCorrect) {
    correctCount++;
    const timeBonus = timer > 0 ? timer : 0;
    const gainedScore = timeBonus * scoreMultiplier;
    score += gainedScore;
    resultEl.innerHTML = `<span style="color:green; font-weight:bold;">✅ ถูกต้อง! +${gainedScore} คะแนน</span>`;
  } else {
    resultEl.innerHTML = `<span style="color:#e35656; font-weight:bold;">❌ ไม่ถูกต้อง</span>`;
  }

  current++;
  if (current < roundItems.length) {
    setTimeout(() => {
      resultEl.innerHTML = '';
      showWaste(current);
    }, 1300);
  } else {
    showFinalScore();
  }
}

function launchConfetti() {
  confetti({ particleCount: 100, spread: 70, origin: { y: 0.6 } });
  setTimeout(() => confetti({ particleCount: 150, spread: 100, origin: { x: 0.1, y: 0.3 } }), 300);
  setTimeout(() => confetti({ particleCount: 150, spread: 100, origin: { x: 0.9, y: 0.3 } }), 600);
  setTimeout(() => confetti({ particleCount: 200, spread: 120, origin: { y: 0.4 } }), 900);
}

function showFinalScore() {
  clearInterval(timerInterval);
  wasteCard.innerHTML = '';

  const mascot = getMascotByScore(score);
  const resultHTML = `
    <div class="result">
      <div class="score-summary">
        🎉 คุณตอบถูกทั้งหมด <strong>${correctCount}</strong> ข้อ จาก ${roundItems.length} ข้อ<br>
        🏆 คะแนนรวมทั้งหมด: <strong>${score}</strong> คะแนน
      </div>

      <div class="mascot-final-box">
        <div class="mascot-img">
          <img src="${mascot.svg}" alt="${mascot.name}" />
        </div>
        <div class="mascot-text">
          <h2 style="font-size: 1.6rem;">🌟 คาแรคเตอร์ของคุณคือ:<br><span class="mascot-label ${mascot.type}">${mascot.label}</span></h2>
          <h3>${mascot.name}</h3>
          <p>${mascot.description}</p>
        </div>
      </div>
    </div>
  `;
  wasteCard.innerHTML = resultHTML;

  launchConfetti();

  const resultEl = document.getElementById('wasteResult');
  if (resultEl) resultEl.innerHTML = '';

  setTimeout(() => {
    localStorage.setItem('waste_score', String(score));
    const elapsedSec = Math.max(0, Math.floor((Date.now() - wasteStartTime) / 1000));
    localStorage.setItem('waste_time', String(elapsedSec));
    window.location.href = "../quiz/result.php";
  }, 2600);
}

/* เน้นคำถาม: แยกสีส่วนชื่อไอเทม vs หางประโยค “ควรทิ้งถัง(ขยะ)ไหน?” */
function colorizeQuestion(text) {
  const re = /(.*?)(\s*)(ควรทิ้งถัง(?:ขยะ)?ไหน(?:\s*\?)?)/;
  const m = text.match(re);
  if (m) {
    const itemPart = m[1].trim();
    const tailPart = m[3].trim();
    return `<span class="q-item">${itemPart}</span> <span class="q-tail">${tailPart}</span>`;
  }
  return `<span class="q-item">${text}</span>`;
}

function showWaste(idx) {
  clearInterval(timerInterval);
  timer = maxTime;
  updateProgress(100);

  const w = roundItems[idx];
  wasteCard.innerHTML = `
    <div class="waste-content">
      <div class="question">${colorizeQuestion(w.question)}</div>
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

  const dragItem = wasteCard.querySelector('.draggable-item');
  dragItem.addEventListener('dragstart', () => {});

  document.querySelectorAll('.bin-drop').forEach(binEl => {
    binEl.ondragover = e => { e.preventDefault(); binEl.classList.add('open','drag-over'); };
    binEl.ondragleave = () => { binEl.classList.remove('open','drag-over'); };
    binEl.ondrop = e => {
      e.preventDefault();
      document.querySelectorAll('.bin-drop').forEach(b => b.classList.remove('open','drag-over'));
      handleWasteAnswer(binEl.dataset.type);
    };
  });

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

/* เริ่มเกม: สร้างเด็ค 10 ชิ้นตามกติกา แล้วเล่น */
document.addEventListener('DOMContentLoaded', () => {
  roundItems = buildRound(wasteItems, 10);
  showWaste(0);
});
