const form = document.getElementById('nameForm');
form.onsubmit = function(e) {
  e.preventDefault();
  let name = document.getElementById('playerNameInput').value.trim();
  if (!name) name = 'Guest' + Math.floor(Math.random()*9000+1000);
  localStorage.setItem('playerName', name);

  const nextGame = localStorage.getItem('chosenGame') || 'waste';
  window.location = nextGame + '.html';
};
