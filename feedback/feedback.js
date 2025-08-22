// Reload button
document.getElementById('reloadBtn')?.addEventListener('click', () => {
  const frame = document.getElementById('gform');
  if (frame) frame.src = frame.src;
});

/*
 * ⭐ PREFILL (ทางเลือก) – ส่งค่าจากเกม เช่น score/name เข้า Google Form อัตโนมัติ
 * วิธีใช้:
 * 1) ไปที่ Google Forms > ⋮ > Get pre-filled link
 * 2) กรอกค่าตัวอย่าง แล้วกด Get link
 * 3) คัดลอกเลข entry.xxxxxxxxxx ของฟิลด์ที่ต้องการ
 * 4) ส่งค่ามาหน้านี้ผ่าน query string เช่น feedback.html?score=1200&name=Moshi
 * 5) นำเลข entry.* มาแทนใน mapping ด้านล่าง แล้วยกเลิกคอมเมนต์โค้ด
 */

// อ่านค่าจาก query string ของเพจนี้ (ถ้ามี)
const params = new URLSearchParams(window.location.search);
const score = params.get('score');  // เช่น 1200
const name  = params.get('name');   // เช่น "Moshi"

// ===== Uncomment เพื่อใช้งาน Prefill =====
// (แทน entry.* ให้ตรงกับฟอร์มจริงของคุณ)
// (ดูหมายเลขได้จาก "Get pre-filled link" ของฟอร์ม)
 /*
(function prefillGoogleForm(){
  const base = 'https://docs.google.com/forms/d/e/1FAIpQLSewB6QMTOYcfYIlYc9eE3R-sh4cPCgXAZD8FJYxo93y_sVWhw/viewform?embedded=true';

  // Map คีย์ท้องถิ่น -> entry.* ของ Google Form
  const map = {
    score: 'entry.1111111111',  // ← ใส่ entry.* ของ “คะแนนรวม”
    name:  'entry.2222222222'   // ← ใส่ entry.* ของ “ชื่อเล่น”
  };

  const prefill = new URLSearchParams();
  if (score) prefill.set(map.score, score);
  if (name)  prefill.set(map.name,  name);

  if ([...prefill.values()].length) {
    const iframe = document.getElementById('gform');
    iframe.src = `${base}&${prefill.toString()}`;
  }
})();
 */
