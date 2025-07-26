// On click, set chosen game and go to enter-name page
document.querySelectorAll('.game-btn').forEach(btn => {
  btn.onclick = () => {
    localStorage.setItem('chosenGame', btn.dataset.game);
    window.location = 'enter-name.html';
  }
});
