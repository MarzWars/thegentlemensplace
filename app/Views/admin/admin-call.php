<?php
// app/Views/admin/admin-call.php
?>
<div class="admin-page-header">
  <h1 class="admin-page-title">Live Call Portal</h1>
  <span style="font-size:0.85rem; color:rgba(196,184,150,0.6);">Monitor and answer incoming client calls instantly.</span>
</div>

<div class="admin-panel" style="margin-bottom: 2rem;">
  <div class="admin-panel-header" style="border-bottom: 1px solid rgba(201, 168, 76, 0.1); padding-bottom: 1rem; margin-bottom: 1.5rem; display:flex; justify-content:space-between; align-items:center;">
    <div>
      <h2 class="admin-panel-title">Proxy Mode Status</h2>
      <p style="margin: 0.25rem 0 0 0; font-size: 0.8rem; color: rgba(196, 184, 150, 0.5);">Must be active for incoming calls to route here.</p>
    </div>
    <div style="display:flex; align-items:center; gap:12px;">
      <span class="status-indicator-dot" style="display:inline-block; width:12px; height:12px; border-radius:50%; background:<?= $proxyMode ? '#28a745' : '#6c757d' ?>; box-shadow: 0 0 10px <?= $proxyMode ? '#28a745' : 'transparent' ?>;"></span>
      <span style="font-weight: 700; color: #f0e8d0; font-size:0.9rem;"><?= $proxyMode ? 'ACTIVE' : 'INACTIVE' ?></span>
    </div>
  </div>

  <div style="font-size: 0.9rem; color: #f0e8d0;">
    <?php if (!$proxyMode): ?>
      <div style="background: rgba(107, 26, 42, 0.1); border: 1px solid rgba(160, 36, 60, 0.2); padding: 1rem; border-radius: 8px; color: #f0b0b8; display:flex; justify-content:space-between; align-items:center;">
        <span>Proxy mode is currently turned off. Turn it on from the main Dashboard to intercept performer calls.</span>
        <a href="<?= BASE_PATH ?>/admin" class="admin-btn admin-btn-sm admin-btn-primary">Dashboard</a>
      </div>
    <?php else: ?>
      <div style="background: rgba(40, 167, 69, 0.08); border: 1px solid rgba(40, 167, 69, 0.2); padding: 1.2rem; border-radius: 8px; color: #a2f2b4; display:flex; justify-content:space-between; align-items:center; flex-wrap: wrap; gap: 15px;">
        <div>
          <strong style="display:block; margin-bottom:0.2rem;">Portal is Online & Listening</strong>
          <span style="font-size:0.8rem; color:rgba(196,184,150,0.7);">Leave this tab open. Ringing sound will play automatically when clients call.</span>
        </div>
        <button id="activate-audio-btn" class="admin-btn admin-btn-sm admin-btn-success">Test Sound & Enable Audio</button>
      </div>
    <?php endif; ?>
  </div>
</div>

<div class="admin-panel">
  <div class="admin-panel-header">
    <h2 class="admin-panel-title">Incoming Ringing Calls</h2>
    <span id="listening-status" style="font-size:.7rem; color:rgba(196,184,150,.4); text-transform: uppercase; letter-spacing: 0.1em; display:flex; align-items:center; gap:6px;">
      <span class="pulse-dot"></span> Polling every 2s...
    </span>
  </div>

  <div id="no-calls-container" style="padding: 4rem 2rem; text-align: center; color: rgba(196, 184, 150, 0.4);">
    <div style="font-size: 2.5rem; margin-bottom: 1rem;">📞</div>
    <h3>No active incoming calls</h3>
    <p style="font-size: 0.85rem; margin-top: 0.25rem;">When a client starts a call, it will appear here instantly.</p>
  </div>

  <div id="calls-list" class="admin-table-wrap" style="display: none;">
    <table class="admin-table">
      <thead>
        <tr>
          <th>Selected Performer</th>
          <th>Caller (Client)</th>
          <th>Waiting Duration</th>
          <th style="text-align: right;">Action</th>
        </tr>
      </thead>
      <tbody id="calls-table-body">
        <!-- Dynically filled -->
      </tbody>
    </table>
  </div>
</div>

<style>
.pulse-dot {
  width: 6px;
  height: 6px;
  background-color: #c9a84c;
  border-radius: 50%;
  animation: pulse-listening 1.5s infinite;
}
@keyframes pulse-listening {
  0% { transform: scale(0.9); opacity: 0.4; }
  50% { transform: scale(1.3); opacity: 1; }
  100% { transform: scale(0.9); opacity: 0.4; }
}
.ringing-row {
  animation: ring-highlight 2s infinite ease-in-out;
  background: rgba(201, 168, 76, 0.03);
}
@keyframes ring-highlight {
  0% { background: rgba(201, 168, 76, 0.02); }
  50% { background: rgba(201, 168, 76, 0.08); }
  100% { background: rgba(201, 168, 76, 0.02); }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const callsList = document.getElementById('calls-list');
  const noCallsContainer = document.getElementById('no-calls-container');
  const tableBody = document.getElementById('calls-table-body');
  const audioBtn = document.getElementById('activate-audio-btn');

  let audioCtx = null;
  let ringInterval = null;
  let audioEnabled = false;

  function initAudio() {
    if (audioCtx) return;
    try {
      audioCtx = new (window.AudioContext || window.webkitAudioContext)();
      audioEnabled = true;
      if (audioBtn) {
        audioBtn.innerText = "Audio Enabled ✓";
        audioBtn.classList.remove('admin-btn-success');
        audioBtn.classList.add('admin-btn-secondary');
        audioBtn.disabled = true;
      }
      
      // Play a short double beep to test
      playBeep();
    } catch(e) {
      console.error("Audio Context Init Failed", e);
    }
  }

  function playBeep() {
    if (!audioCtx) return;
    const osc = audioCtx.createOscillator();
    const gain = audioCtx.createGain();
    osc.frequency.value = 800;
    osc.connect(gain);
    gain.connect(audioCtx.destination);
    gain.gain.setValueAtTime(0, audioCtx.currentTime);
    gain.gain.linearRampToValueAtTime(0.15, audioCtx.currentTime + 0.05);
    gain.gain.linearRampToValueAtTime(0, audioCtx.currentTime + 0.2);
    osc.start();
    osc.stop(audioCtx.currentTime + 0.25);
  }

  if (audioBtn) {
    audioBtn.addEventListener('click', initAudio);
  }

  function startRingingSound() {
    if (!audioEnabled || ringInterval) return;
    
    // Play ringing dual tone periodically
    ringInterval = setInterval(() => {
      if (!audioCtx || audioCtx.state === 'suspended') return;
      
      const t = audioCtx.currentTime;
      const osc1 = audioCtx.createOscillator();
      const osc2 = audioCtx.createOscillator();
      const gain = audioCtx.createGain();
      
      osc1.frequency.value = 440;
      osc2.frequency.value = 480;
      
      osc1.connect(gain);
      osc2.connect(gain);
      gain.connect(audioCtx.destination);
      
      gain.gain.setValueAtTime(0, t);
      gain.gain.linearRampToValueAtTime(0.15, t + 0.1);
      gain.gain.setValueAtTime(0.15, t + 1.2);
      gain.gain.linearRampToValueAtTime(0, t + 1.5);
      
      osc1.start();
      osc2.start();
      osc1.stop(t + 1.6);
      osc2.stop(t + 1.6);
    }, 3000);
  }

  function stopRingingSound() {
    if (ringInterval) {
      clearInterval(ringInterval);
      ringInterval = null;
    }
  }

  function formatTime(seconds) {
    const mins = Math.floor(seconds / 60);
    const secs = seconds % 60;
    return `${mins}:${secs.toString().padStart(2, '0')}`;
  }

  function pollCalls() {
    fetch('<?= BASE_PATH ?>/admin/call/pending')
      .then(res => res.json())
      .then(calls => {
        if (calls && calls.length > 0) {
          // Play sound
          startRingingSound();

          noCallsContainer.style.display = 'none';
          callsList.style.display = 'block';

          // Build rows
          tableBody.innerHTML = '';
          calls.forEach(call => {
            const tr = document.createElement('tr');
            tr.className = 'ringing-row';
            
            const tdPerformer = document.createElement('td');
            tdPerformer.innerHTML = `<strong style="color:#c9a84c;">${escapeHtml(call.performer_name)}</strong>`;
            
            const tdUser = document.createElement('td');
            tdUser.innerText = escapeHtml(call.username);
            
            const tdWait = document.createElement('td');
            tdWait.innerText = formatTime(call.wait_seconds);
            
            const tdAction = document.createElement('td');
            tdAction.style.textAlign = 'right';
            
            const btn = document.createElement('button');
            btn.className = 'admin-btn admin-btn-sm admin-btn-success';
            btn.innerText = 'Answer Call';
            btn.addEventListener('click', function() {
              btn.disabled = true;
              btn.innerText = 'Connecting...';
              answerCall(call.uuid);
            });
            
            tdAction.appendChild(btn);
            tr.appendChild(tdPerformer);
            tr.appendChild(tdUser);
            tr.appendChild(tdWait);
            tr.appendChild(tdAction);
            tableBody.appendChild(tr);
          });
        } else {
          stopRingingSound();
          callsList.style.display = 'none';
          noCallsContainer.style.display = 'block';
        }
      })
      .catch(err => {
        console.error("Failed to poll calls", err);
      });
  }

  function answerCall(uuid) {
    stopRingingSound();
    
    const formData = new FormData();
    fetch('<?= BASE_PATH ?>/admin/call/answer/' + uuid, {
      method: 'POST',
      body: formData
    })
    .then(res => res.json())
    .then(data => {
      if (data.success && data.room_url) {
        window.location.href = data.room_url;
      } else {
        alert('Could not answer call: ' + (data.message || 'Unknown error'));
        pollCalls(); // refresh immediately
      }
    })
    .catch(err => {
      console.error(err);
      alert('Network error');
      pollCalls();
    });
  }

  function escapeHtml(str) {
    return str.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
  }

  // Start polling
  pollCalls();
  setInterval(pollCalls, 2000);

  // Auto-init audio on any page interaction if they haven't explicitly clicked the button
  document.addEventListener('click', function() {
    if (!audioCtx) {
      initAudio();
    }
  }, { once: true });
});
</script>
