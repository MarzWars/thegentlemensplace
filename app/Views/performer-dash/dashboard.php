<?php
// app/Views/performer-dash/dashboard.php

// Helper: format seconds into "Xh Ym" or "Ym Xs"
function fmtDuration(int $secs): string {
    if ($secs <= 0) return '0m 0s';
    $h = (int)floor($secs / 3600);
    $m = (int)floor(($secs % 3600) / 60);
    $s = $secs % 60;
    if ($h > 0) return "{$h}h {$m}m";
    return "{$m}m {$s}s";
}

$cs = $callStats ?? ['total_calls' => 0, 'total_seconds' => 0, 'total_earned' => 0];
?>
<script src="https://js.pusher.com/beams/2.1.0/push-notifications-cdn.js"></script>

<div class="dashboard-wrapper">
    <div class="dashboard-grid">
        <!-- Status Card -->
        <div class="dash-card status-card">
            <h3>Status Panel</h3>
            <div class="status-toggle-wrapper">
                <span id="status-badge" class="badge <?= $performer['online_status'] ? 'online' : 'offline' ?>">
                    <?= $performer['online_status'] ? 'ONLINE' : 'OFFLINE' ?>
                </span>
                <button id="toggle-status-btn" class="btn btn-primary">
                    Toggle Status
                </button>
            </div>
            <p class="status-hint">Toggle online to start receiving WebRTC voice calls and streaming live.</p>
        </div>

        <!-- Earnings Summary -->
        <div class="dash-card earnings-card">
            <h3>Earnings</h3>
            <div class="earnings-value"><?= CURRENCY ?> <?= number_format((float)$performer['earnings_balance'], 2) ?></div>
            <p class="earnings-hint">Total Earned: <?= CURRENCY ?> <?= number_format((float)$performer['earnings_total'], 2) ?></p>
        </div>

        <!-- Video Call Settings Card -->
        <div class="dash-card settings-card">
            <h3>Video Call Settings</h3>
            <form action="<?= BASE_PATH ?>/performer-dash/settings" method="POST" class="settings-form">
                <input type="hidden" name="csrf_token" value="<?= \App\Core\CSRF::generate() ?>">
                
                <div class="form-group toggle-group">
                    <label class="switch-label" for="video_enabled">
                        <span class="label-text">Enable Video Calls</span>
                        <div class="switch">
                            <input type="checkbox" id="video_enabled" name="video_enabled" value="1" <?= $performer['video_enabled'] ? 'checked' : '' ?>>
                            <span class="slider round"></span>
                        </div>
                    </label>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="video_min_credits">Min Entry Fee (Credits)</label>
                        <input type="number" id="video_min_credits" name="video_min_credits" step="0.01" min="0" value="<?= number_format((float)$performer['video_min_credits'], 2, '.', '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="video_min_minutes">Included Minutes</label>
                        <input type="number" id="video_min_minutes" name="video_min_minutes" min="1" value="<?= (int)$performer['video_min_minutes'] ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="video_rate_per_minute">Rate Per Minute After (Credits)</label>
                    <input type="number" id="video_rate_per_minute" name="video_rate_per_minute" step="0.01" min="0" value="<?= number_format((float)$performer['video_rate_per_minute'], 2, '.', '') ?>" required>
                </div>

                <p class="settings-hint">Clients pay the Entry Fee for the initial Included Minutes, then the Per-Minute rate applies.</p>

                <button type="submit" class="btn btn-primary btn-block">Save Video Settings</button>
            </form>
        </div>

        <!-- Aggregate Stat Cards -->
        <div class="dash-card stat-card">
            <div class="stat-icon">📞</div>
            <div class="stat-value"><?= number_format((int)$cs['total_calls']) ?></div>
            <div class="stat-label">Completed Calls</div>
        </div>

        <div class="dash-card stat-card">
            <div class="stat-icon">⏱️</div>
            <div class="stat-value"><?= fmtDuration((int)$cs['total_seconds']) ?></div>
            <div class="stat-label">Total Talk Time</div>
        </div>

        <div class="dash-card stat-card">
            <div class="stat-icon">💰</div>
            <div class="stat-value"><?= CURRENCY ?> <?= number_format((float)$cs['total_earned'], 2) ?></div>
            <div class="stat-label">Lifetime Earned</div>
        </div>

        <!-- Recent Calls -->
        <div class="dash-card table-card">
            <h3>Recent Calls</h3>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Status</th>
                            <th>Duration</th>
                            <th>Earnings</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recentCalls)): ?>
                            <tr>
                                <td colspan="5" class="empty-state">No calls recorded yet.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recentCalls as $c): ?>
                                <tr>
                                    <td><?= htmlspecialchars($c['username']) ?></td>
                                    <td><span class="badge-status <?= $c['status'] ?>"><?= strtoupper($c['status']) ?></span></td>
                                    <td><?= fmtDuration((int)$c['duration_seconds']) ?></td>
                                    <td><?= CURRENCY ?> <?= number_format((float)$c['performer_earnings'], 2) ?></td>
                                    <td><?= date('d M Y H:i', strtotime($c['created_at'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Premium Fullscreen Incoming Call Modal -->
<div id="incoming-call-modal" class="incoming-modal" style="display: none;">
    <div class="modal-overlay"></div>
    <div class="modal-content">
        <div class="caller-avatar">
            <div class="modal-pulse-ring"></div>
            <div class="modal-avatar-placeholder">👤</div>
        </div>
        <h2 class="caller-title" id="caller-title-label">Incoming Voice Call</h2>
        <p class="caller-name-label">User: <strong id="caller-username">Client</strong></p>
        <p class="caller-sub" id="caller-sub-label">Wants to start a private voice call room</p>

        <div class="modal-actions">
            <button id="modal-decline-btn" class="modal-btn decline">Decline</button>
            <button id="modal-accept-btn" class="modal-btn accept">Accept</button>
        </div>
    </div>
</div>

<!-- CSRF Token helper -->
<input type="hidden" id="csrf-token" value="<?= \App\Core\CSRF::generate() ?>">

<style>
.dashboard-wrapper {
    padding: 2rem;
    min-height: calc(100vh - 120px);
    background: #0a0805;
    color: #c4b896;
    font-family: sans-serif;
}

.dashboard-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
    max-width: 1200px;
    margin: 0 auto;
}

.dash-card {
    background: #111008;
    border: 1px solid rgba(201, 168, 76, 0.15);
    padding: 1.8rem;
    border-radius: 16px;
}

.table-card {
    grid-column: 1 / -1;
}

h3 {
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.15em;
    color: #f0e8d0;
    margin-bottom: 1.2rem;
    border-bottom: 1px solid rgba(201, 168, 76, 0.1);
    padding-bottom: 0.6rem;
}

.status-toggle-wrapper {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1rem;
}

.badge {
    padding: 0.4rem 1rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 700;
}

.badge.online {
    background: rgba(45, 106, 79, 0.2);
    border: 1px solid #52b788;
    color: #a8e6c8;
}

.badge.offline {
    background: rgba(107, 26, 42, 0.2);
    border: 1px solid #a0243c;
    color: #f0b0b8;
}

.btn {
    padding: 0.6rem 1.2rem;
    border-radius: 8px;
    font-size: 0.8rem;
    font-weight: 600;
    cursor: pointer;
    border: none;
}

.btn-primary {
    background: #c9a84c;
    color: #0a0805;
}

.btn-primary:hover {
    background: #e0c06a;
}

.earnings-value {
    font-size: 2.2rem;
    font-weight: 700;
    color: #f0e8d0;
    margin-bottom: 0.5rem;
}

.status-hint, .earnings-hint {
    font-size: 0.75rem;
    color: rgba(196, 184, 150, 0.5);
}

/* ── Aggregate stat cards ────────────────────── */
.stat-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    padding: 2rem 1.5rem;
    min-height: 140px;
    background: linear-gradient(145deg, rgba(201, 168, 76, 0.05) 0%, #111008 100%);
    border-color: rgba(201, 168, 76, 0.2);
    transition: border-color 0.2s ease, transform 0.2s ease;
}

.stat-card:hover {
    border-color: rgba(201, 168, 76, 0.4);
    transform: translateY(-2px);
}

.stat-icon {
    font-size: 1.6rem;
    margin-bottom: 0.6rem;
    line-height: 1;
}

.stat-value {
    font-size: 1.8rem;
    font-weight: 700;
    color: #f0e8d0;
    line-height: 1;
    margin-bottom: 0.4rem;
    letter-spacing: -0.02em;
}

.stat-label {
    font-size: 0.7rem;
    text-transform: uppercase;
    letter-spacing: 0.15em;
    color: rgba(196, 184, 150, 0.5);
}


table {
    width: 100%;
    border-collapse: collapse;
}

th, td {
    padding: 1rem;
    text-align: left;
    border-bottom: 1px solid rgba(201, 168, 76, 0.08);
    font-size: 0.85rem;
}

th {
    color: rgba(196, 184, 150, 0.6);
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.7rem;
    letter-spacing: 0.1em;
}

.badge-status {
    font-size: 0.7rem;
    font-weight: 700;
    padding: 0.2rem 0.6rem;
    border-radius: 4px;
}

.badge-status.ringing { background: #9a7a30; color: #fff; }
.badge-status.in_progress { background: #2d6a4f; color: #fff; }
.badge-status.completed { background: #333; color: #ccc; }
.badge-status.declined { background: #6b1a2a; color: #f0b0b8; }

/* Modal CSS */
.incoming-modal {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: default;
}

.modal-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.85);
    backdrop-filter: blur(15px);
}

.modal-content {
    background: #14120a;
    border: 1px solid rgba(201, 168, 76, 0.3);
    border-radius: 24px;
    padding: 3rem;
    max-width: 400px;
    width: 90%;
    text-align: center;
    position: relative;
    z-index: 10;
    box-shadow: 0 20px 60px rgba(0,0,0,0.8);
}

.caller-avatar {
    position: relative;
    width: 100px;
    height: 100px;
    margin: 0 auto 1.5rem;
}

.modal-avatar-placeholder {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    background: #1a170e;
    border: 2px solid #c9a84c;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 2.5rem;
    position: relative;
    z-index: 2;
}

.modal-pulse-ring {
    position: absolute;
    top: -4px;
    left: -4px;
    right: -4px;
    bottom: -4px;
    border-radius: 50%;
    border: 1px solid #c9a84c;
    animation: modal-pulse 1.8s infinite ease-out;
}

@keyframes modal-pulse {
    0% { transform: scale(0.95); opacity: 1; }
    100% { transform: scale(1.5); opacity: 0; }
}

.caller-title {
    font-size: 1.4rem;
    color: #f0e8d0;
    margin-bottom: 0.5rem;
}

.caller-name-label {
    font-size: 1.1rem;
    color: #c9a84c;
    margin-bottom: 0.5rem;
}

.caller-sub {
    font-size: 0.8rem;
    color: rgba(196, 184, 150, 0.5);
    margin-bottom: 2.5rem;
}

.modal-actions {
    display: flex;
    gap: 1rem;
}

.modal-btn {
    flex: 1;
    padding: 0.9rem;
    border-radius: 12px;
    font-size: 0.85rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    cursor: pointer;
    border: none;
    transition: all 0.3s ease;
}

.modal-btn.accept {
    background: #2d6a4f;
    color: #fff;
    border: 1px solid #52b788;
}

.modal-btn.accept:hover {
    background: #52b788;
}

.modal-btn.decline {
    background: #6b1a2a;
    color: #f0b0b8;
    border: 1px solid rgba(160, 36, 60, 0.4);
}

.modal-btn.decline:hover {
    background: #a0243c;
}

/* Settings Form CSS */
.settings-form {
    display: flex;
    flex-direction: column;
    gap: 1.2rem;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.form-row {
    display: flex;
    flex-direction: column;
    gap: 1.2rem;
}

.form-group label {
    font-size: 0.75rem;
    color: rgba(196, 184, 150, 0.75);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    font-weight: 600;
}

.form-group input[type="number"] {
    background: #0a0805;
    border: 1px solid rgba(201, 168, 76, 0.2);
    border-radius: 8px;
    padding: 0.75rem;
    color: #f0e8d0;
    font-size: 0.9rem;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.form-group input[type="number"]:focus {
    outline: none;
    border-color: #c9a84c;
    box-shadow: 0 0 0 2px rgba(201, 168, 76, 0.2);
}

/* Switch Toggle Styling */
.toggle-group {
    border-bottom: 1px solid rgba(201, 168, 76, 0.1);
    padding-bottom: 1rem;
}

.switch-label {
    display: flex;
    justify-content: space-between;
    align-items: center;
    cursor: pointer;
}

.label-text {
    font-size: 0.85rem !important;
    font-weight: 600;
    color: #f0e8d0 !important;
}

.switch {
    position: relative;
    display: inline-block;
    width: 50px;
    height: 26px;
}

.switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #222;
    transition: .4s;
    border: 1px solid rgba(201, 168, 76, 0.2);
}

.slider:before {
    position: absolute;
    content: "";
    height: 18px;
    width: 18px;
    left: 3px;
    bottom: 3px;
    background-color: #c4b896;
    transition: .4s;
}

input:checked + .slider {
    background-color: rgba(201, 168, 76, 0.2);
    border-color: #c9a84c;
}

input:checked + .slider:before {
    background-color: #c9a84c;
    transform: translateX(24px);
}

.slider.round {
    border-radius: 34px;
}

.slider.round:before {
    border-radius: 50%;
}

.btn-block {
    width: 100%;
}

.settings-hint {
    font-size: 0.75rem;
    color: rgba(196, 184, 150, 0.5);
    line-height: 1.4;
    margin: 0;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Pusher Beams Initialization
    const beamsClient = new PusherPushNotifications.Client({
        instanceId: 'f69936f5-f652-4ac6-8f02-4d2a048ca15f',
    });

    beamsClient.start()
        .then(() => beamsClient.addDeviceInterest('performer_<?= $performer['id'] ?>'))
        .then(() => console.log('Subscribed to Beams interest: performer_<?= $performer['id'] ?>'))
        .catch(console.error);

    // 2. Incoming call checking (polling logic as fallback/helper)
    const checkIncomingUrl = '<?= BASE_PATH ?>/call/incoming-check';
    let currentCallUuid = null;
    let callCheckingInterval = null;
    let soundSynth = null;
    let ringInterval = null;

    function startRingingSound() {
        if (!soundSynth) {
            soundSynth = new (window.AudioContext || window.webkitAudioContext)();
        }
        function playTone() {
            let osc = soundSynth.createOscillator();
            let gainNode = soundSynth.createGain();
            osc.connect(gainNode);
            gainNode.connect(soundSynth.destination);
            
            osc.type = 'triangle';
            osc.frequency.setValueAtTime(320, soundSynth.currentTime);
            gainNode.gain.setValueAtTime(0.3, soundSynth.currentTime);
            gainNode.gain.exponentialRampToValueAtTime(0.01, soundSynth.currentTime + 1.2);
            
            osc.start();
            osc.stop(soundSynth.currentTime + 1.3);
        }
        playTone();
        ringInterval = setInterval(playTone, 2000);
    }

    function stopRingingSound() {
        if (ringInterval) {
            clearInterval(ringInterval);
            ringInterval = null;
        }
    }

    function showIncomingCallModal(uuid, username, callType) {
        currentCallUuid = uuid;
        document.getElementById('caller-username').innerText = username;
        
        // Dynamically adjust title and subtitle based on callType
        const titleLabel = document.getElementById('caller-title-label');
        const subLabel = document.getElementById('caller-sub-label');
        if (titleLabel && subLabel) {
            if (callType === 'video') {
                titleLabel.innerText = 'Incoming Video Call';
                subLabel.innerText = 'Wants to start a private video call room';
            } else {
                titleLabel.innerText = 'Incoming Voice Call';
                subLabel.innerText = 'Wants to start a private voice call room';
            }
        }

        document.getElementById('incoming-call-modal').style.display = 'flex';
        try { startRingingSound(); } catch(e){}
    }

    function hideIncomingCallModal() {
        document.getElementById('incoming-call-modal').style.display = 'none';
        stopRingingSound();
        currentCallUuid = null;
    }

    // Poll for active calls
    callCheckingInterval = setInterval(function() {
        if (currentCallUuid) return; // Modal already open
        
        fetch(checkIncomingUrl)
            .then(res => res.json())
            .then(data => {
                if (data.incoming) {
                    showIncomingCallModal(data.uuid, data.username, data.type);
                }
            })
            .catch(err => console.error(err));
    }, 3000);

    // Accept / Decline handlers
    document.getElementById('modal-accept-btn').addEventListener('click', function() {
        if (!currentCallUuid) return;
        const formData = new FormData();
        formData.append('csrf_token', document.getElementById('csrf-token').value);

        fetch('<?= BASE_PATH ?>/call/accept/' + currentCallUuid, {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                hideIncomingCallModal();
                // Use absolute URL to avoid BASE_PATH issues
                window.location.href = window.location.origin + data.room_url;
            } else {
                alert('Accept failed.');
                hideIncomingCallModal();
            }
        })
        .catch(err => console.error(err));
    });

    document.getElementById('modal-decline-btn').addEventListener('click', function() {
        if (!currentCallUuid) return;
        const formData = new FormData();
        formData.append('csrf_token', document.getElementById('csrf-token').value);

        fetch('<?= BASE_PATH ?>/call/decline/' + currentCallUuid, {
            method: 'POST',
            body: formData
        })
        .then(() => {
            hideIncomingCallModal();
        })
        .catch(err => console.error(err));
    });

    // Toggle performer status (Online/Offline)
    document.getElementById('toggle-status-btn').addEventListener('click', function() {
        const formData = new FormData();
        formData.append('csrf_token', document.getElementById('csrf-token').value);

        fetch('<?= BASE_PATH ?>/performer-dash/status', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            const badge = document.getElementById('status-badge');
            if (data.online) {
                badge.innerText = 'ONLINE';
                badge.className = 'badge online';
            } else {
                badge.innerText = 'OFFLINE';
                badge.className = 'badge offline';
            }
        })
        .catch(err => console.error(err));
    });
});
</script>
