// ==============================
// loading.js — SHAMS School
// ==============================

(function () {
  const fillEl  = document.querySelector('.progress-fill');
  const pctEl   = document.querySelector('.progress-pct');
  const labelEl = document.querySelector('.loading-label');
  const screen  = document.querySelector('.loading-screen');

  const messages = [
    'Loading resources…',
    'Setting up school portal…',
    'Preparing content…',
    'Almost there…',
    'Welcome to SHAMS!'
  ];

  let progress = 0;
  let msgIdx   = 0;

  // Simulate realistic, network-speed-sensitive loading
  // We use a variable tick speed: faster at start/end, slower in middle
  function getTickSize(p) {
    if (p < 20)  return Math.random() * 5 + 3;   // fast start
    if (p < 60)  return Math.random() * 2 + 0.8; // slow middle (network)
    if (p < 85)  return Math.random() * 3 + 1.5;
    return Math.random() * 4 + 2;                 // fast finish
  }

  function getDelay(p) {
    if (p < 20)  return Math.random() * 60  + 30;
    if (p < 60)  return Math.random() * 200 + 80;  // slowest — simulates network
    if (p < 85)  return Math.random() * 100 + 50;
    return Math.random() * 50 + 20;
  }

  function tick() {
    if (progress >= 100) {
      finish();
      return;
    }

    progress = Math.min(100, progress + getTickSize(progress));
    fillEl.style.width = progress + '%';
    pctEl.textContent  = Math.round(progress) + '%';

    // Update message at intervals
    const newMsgIdx = Math.floor((progress / 100) * messages.length);
    if (newMsgIdx !== msgIdx && newMsgIdx < messages.length) {
      msgIdx = newMsgIdx;
      labelEl.style.opacity = '0';
      setTimeout(() => {
        labelEl.textContent  = messages[msgIdx];
        labelEl.style.opacity = '1';
      }, 150);
    }

    setTimeout(tick, getDelay(progress));
  }

  function finish() {
    fillEl.style.width     = '100%';
    pctEl.textContent      = '100%';
    labelEl.textContent    = 'Welcome to SHAMS!';

    setTimeout(() => {
      screen.classList.add('exit');
      setTimeout(() => {
        window.location.href = 'index.html';
      }, 520);
    }, 600);
  }

  // Start after a short initial delay
  setTimeout(tick, 400);
})();
