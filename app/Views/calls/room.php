<?php
// app/Views/calls/room.php
?>
<script src="https://cdn.jsdelivr.net/npm/livekit-client/dist/livekit-client.umd.min.js"></script>

<div class="room-wrapper">
    <div class="room-container <?= $call['type'] === 'video' ? 'video-mode' : '' ?>">
        <?php if ($call['type'] === 'video'): ?>
            <div class="video-grid-wrapper">
                <div class="main-video-container">
                    <video id="remote-video" autoplay playsinline></video>
                    <div id="video-placeholder" class="video-placeholder">
                        <div class="avatar-glow"></div>
                        <?php if (!empty($performer['profile_photo'])): ?>
                            <img class="profile-avatar active-pulse" src="<?= BASE_PATH ?>/<?= htmlspecialchars($performer['profile_photo']) ?>" alt="<?= htmlspecialchars($performer['display_name']) ?>">
                        <?php else: ?>
                            <div class="avatar-placeholder"><?= strtoupper(substr($performer['display_name'], 0, 1)) ?></div>
                        <?php endif; ?>
                        <p class="placeholder-text">Waiting for video stream...</p>
                    </div>
                    <button id="fullscreen-btn" class="video-action-btn" title="Toggle Fullscreen">⛶</button>
                    <div class="pip-video-container">
                        <video id="local-video" autoplay playsinline muted></video>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="room-sidebar">
            <div class="call-header">
                <span class="secure-badge"><i class="fas fa-lock"></i> Encrypted Call</span>
                <span id="duration" class="call-duration">00:00</span>
            </div>

            <?php if ($call['type'] === 'voice'): ?>
                <div class="performer-profile">
                    <div class="avatar-glow"></div>
                    <?php if (!empty($performer['profile_photo'])): ?>
                        <img class="profile-avatar active-pulse" src="<?= BASE_PATH ?>/<?= htmlspecialchars($performer['profile_photo']) ?>" alt="<?= htmlspecialchars($performer['display_name']) ?>">
                    <?php else: ?>
                        <div class="avatar-placeholder"><?= strtoupper(substr($performer['display_name'], 0, 1)) ?></div>
                    <?php endif; ?>
                    <h2 class="profile-name"><?= htmlspecialchars($performer['display_name']) ?></h2>
                    <p id="call-status-label" class="profile-status">Connecting audio...</p>
                </div>

                <div class="visualizer-container">
                    <div class="wave-bar"></div>
                    <div class="wave-bar"></div>
                    <div class="wave-bar"></div>
                    <div class="wave-bar"></div>
                    <div class="wave-bar"></div>
                    <div class="wave-bar"></div>
                    <div class="wave-bar"></div>
                    <div class="wave-bar"></div>
                </div>
            <?php else: ?>
                <div class="video-hud-info">
                    <h2 class="profile-name"><?= htmlspecialchars($performer['display_name']) ?></h2>
                    <p id="call-status-label" class="profile-status">Connecting video...</p>
                </div>
            <?php endif; ?>

            <?php if (!$isPerformer): ?>
                <div class="billing-hud">
                    <span class="rate-label">Rate: <?= htmlspecialchars($call['rate_per_minute']) ?> cr/min</span>
                    <span id="credit-balance" class="balance-label">Balance: Loading...</span>
                </div>
            <?php endif; ?>

            <div class="call-controls">
                <button id="mute-btn" class="control-btn" title="Mute Microphone">
                    <span class="icon">🎙️</span>
                </button>
                <?php if ($call['type'] === 'video'): ?>
                    <button id="camera-btn" class="control-btn active" title="Toggle Camera">
                        <span class="icon">📷</span>
                    </button>
                    <button id="chat-btn" class="control-btn" title="Toggle Chat">
                        <span class="icon">💬</span>
                        <span id="chat-badge" class="chat-badge" style="display: none;"></span>
                    </button>
                <?php endif; ?>
                <button id="hangup-btn" class="control-btn hangup" title="End Call">
                    <span class="icon">❌</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Chat Drawer -->
    <?php if ($call['type'] === 'video'): ?>
        <div id="chat-drawer" class="chat-drawer">
            <div class="chat-header">
                <h3>Room Chat</h3>
                <button id="close-chat-btn" class="close-chat-btn">&times;</button>
            </div>
            <div id="chat-messages" class="chat-messages">
                <div class="system-message">Secure chat room initiated. Text messages are peer-to-peer.</div>
            </div>
            <form id="chat-form" class="chat-form" onsubmit="return false;">
                <input type="text" id="chat-input" placeholder="Type a message..." autocomplete="off">
                <button type="submit" id="chat-send-btn">Send</button>
            </form>
        </div>
    <?php endif; ?>
</div>

<style>
.room-wrapper {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: calc(100vh - 120px);
    background: radial-gradient(circle at center, #15120e 0%, #050403 100%);
    color: #f0e8d0;
    font-family: sans-serif;
    position: relative;
    overflow: hidden;
}

.room-container {
    background: rgba(20, 18, 10, 0.4);
    border: 1px solid rgba(201, 168, 76, 0.15);
    border-radius: 28px;
    padding: 3rem;
    backdrop-filter: blur(20px);
    width: 90%;
    max-width: 440px;
    text-align: center;
    box-shadow: 0 30px 60px rgba(0, 0, 0, 0.6);
    transition: max-width 0.3s ease, width 0.3s ease;
}

/* Layout in Video Mode */
.room-container.video-mode {
    max-width: 960px;
    width: 95%;
    display: grid;
    grid-template-columns: 1.8fr 1fr;
    gap: 2.5rem;
    align-items: center;
    text-align: left;
}

.room-sidebar {
    display: flex;
    flex-direction: column;
    justify-content: center;
    width: 100%;
}

.video-grid-wrapper {
    width: 100%;
    position: relative;
    border-radius: 20px;
    overflow: hidden;
    background: #000;
    aspect-ratio: 16/9;
    border: 1px solid rgba(201, 168, 76, 0.2);
    box-shadow: 0 10px 30px rgba(0,0,0,0.5);
}

.main-video-container {
    width: 100%;
    height: 100%;
    position: relative;
}

#remote-video {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: none;
}

.video-placeholder {
    position: absolute;
    inset: 0;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    background: #111008;
    z-index: 5;
}

.placeholder-text {
    margin-top: 1rem;
    font-size: 0.85rem;
    color: rgba(196, 184, 150, 0.6);
    letter-spacing: 0.05em;
}

.pip-video-container {
    position: absolute;
    bottom: 15px;
    right: 15px;
    width: 128px;
    height: 72px;
    border-radius: 12px;
    overflow: hidden;
    border: 2px solid #c9a84c;
    background: #000;
    box-shadow: 0 5px 15px rgba(0,0,0,0.5);
    z-index: 10;
    transition: all 0.3s ease;
}

.video-action-btn {
    position: absolute;
    top: 15px;
    left: 15px;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    border: 1px solid rgba(201, 168, 76, 0.3);
    background: rgba(17, 16, 8, 0.6);
    color: #f0e8d0;
    font-size: 1.1rem;
    cursor: pointer;
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 12;
    backdrop-filter: blur(5px);
    transition: all 0.3s ease;
}

.video-action-btn:hover {
    border-color: #c9a84c;
    background: rgba(17, 16, 8, 0.85);
    transform: scale(1.05);
}

.video-grid-wrapper:fullscreen {
    width: 100vw;
    height: 100vh;
    border-radius: 0;
    border: none;
    background: #000;
}

.video-grid-wrapper:fullscreen #remote-video {
    width: 100%;
    height: 100%;
    object-fit: contain;
}

.video-grid-wrapper:-webkit-full-screen {
    width: 100vw;
    height: 100vh;
    border-radius: 0;
    border: none;
    background: #000;
}

.video-grid-wrapper:-webkit-full-screen #remote-video {
    width: 100%;
    height: 100%;
    object-fit: contain;
}

#local-video {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.video-hud-info {
    text-align: center;
    margin-bottom: 2rem;
}

.room-container.video-mode .video-hud-info {
    text-align: left;
}

.video-hud-info .profile-name {
    margin-top: 0;
}

.call-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2.5rem;
}

.secure-badge {
    font-size: 0.7rem;
    text-transform: uppercase;
    letter-spacing: 0.15em;
    color: rgba(196, 184, 150, 0.5);
    background: rgba(201, 168, 76, 0.08);
    padding: 0.4rem 0.8rem;
    border-radius: 20px;
    border: 1px solid rgba(201, 168, 76, 0.1);
}

.call-duration {
    font-size: 1rem;
    font-weight: 700;
    color: #c9a84c;
    letter-spacing: 0.05em;
}

.performer-profile {
    position: relative;
    margin-bottom: 2.5rem;
}

.avatar-glow {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 170px;
    height: 170px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(201, 168, 76, 0.15) 0%, rgba(201, 168, 76, 0) 70%);
    filter: blur(10px);
    z-index: 1;
}

.profile-avatar {
    width: 140px;
    height: 140px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #c9a84c;
    position: relative;
    z-index: 2;
    box-shadow: 0 15px 35px rgba(0,0,0,0.5);
}

.avatar-placeholder {
    width: 140px;
    height: 140px;
    border-radius: 50%;
    background: #111008;
    border: 3px solid #c9a84c;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 3.5rem;
    font-weight: 700;
    color: #c9a84c;
    margin: 0 auto;
    position: relative;
    z-index: 2;
    box-shadow: 0 15px 35px rgba(0,0,0,0.5);
}

.profile-name {
    font-size: 1.6rem;
    font-weight: 600;
    margin-top: 1.5rem;
    margin-bottom: 0.3rem;
    color: #f0e8d0;
}

.profile-status {
    font-size: 0.85rem;
    color: rgba(196, 184, 150, 0.6);
    letter-spacing: 0.05em;
}

.visualizer-container {
    display: flex;
    justify-content: center;
    align-items: flex-end;
    gap: 6px;
    height: 40px;
    margin-bottom: 2.5rem;
}

.wave-bar {
    width: 3px;
    height: 5px;
    background: #c9a84c;
    border-radius: 10px;
    transition: height 0.1s ease;
}

.billing-hud {
    display: flex;
    justify-content: space-around;
    align-items: center;
    background: rgba(201, 168, 76, 0.04);
    border: 1px solid rgba(201, 168, 76, 0.1);
    padding: 0.8rem;
    border-radius: 16px;
    margin-bottom: 2.5rem;
    font-size: 0.85rem;
}

.rate-label {
    color: rgba(196, 184, 150, 0.7);
}

.balance-label {
    color: #c9a84c;
    font-weight: 700;
}

.call-controls {
    display: flex;
    justify-content: center;
    gap: 20px;
}

.room-container.video-mode .call-controls {
    justify-content: flex-start;
}

.control-btn {
    width: 54px;
    height: 54px;
    border-radius: 50%;
    border: 1px solid rgba(201, 168, 76, 0.2);
    background: rgba(17, 16, 8, 0.8);
    color: #f0e8d0;
    font-size: 1.25rem;
    cursor: pointer;
    display: flex;
    justify-content: center;
    align-items: center;
    transition: all 0.3s ease;
    box-shadow: 0 4px 10px rgba(0,0,0,0.3);
    position: relative;
}

.chat-badge {
    position: absolute;
    top: 2px;
    right: 2px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background-color: #ff3b30;
    border: 2px solid #111008;
    box-shadow: 0 0 8px #ff3b30;
    animation: chat-badge-pulse 1.5s infinite;
}

@keyframes chat-badge-pulse {
    0% { transform: scale(0.95); opacity: 1; }
    50% { transform: scale(1.15); opacity: 0.8; }
    100% { transform: scale(0.95); opacity: 1; }
}

.control-btn:hover {
    border-color: #c9a84c;
    transform: translateY(-2px);
    box-shadow: 0 6px 15px rgba(201, 168, 76, 0.2);
}

.control-btn.active {
    background: #c9a84c;
    color: #0a0805;
}

.control-btn.hangup {
    background: #6b1a2a;
    border-color: rgba(160, 36, 60, 0.4);
    color: #f0b0b8;
}

.control-btn.hangup:hover {
    background: #a0243c;
    box-shadow: 0 6px 15px rgba(160, 36, 60, 0.4);
}

/* Chat Drawer Styling */
.chat-drawer {
    position: fixed;
    top: 0;
    right: -360px;
    width: 340px;
    height: calc(100vh - 120px);
    margin-top: 60px;
    background: rgba(20, 18, 10, 0.95);
    border-left: 1px solid rgba(201, 168, 76, 0.2);
    box-shadow: -10px 0 30px rgba(0,0,0,0.5);
    display: flex;
    flex-direction: column;
    z-index: 999;
    transition: right 0.3s ease;
    backdrop-filter: blur(20px);
}

.chat-drawer.open {
    right: 0;
}

.chat-header {
    padding: 1.2rem 1.5rem;
    border-bottom: 1px solid rgba(201, 168, 76, 0.1);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.chat-header h3 {
    margin: 0;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: #f0e8d0;
}

.close-chat-btn {
    background: none;
    border: none;
    color: rgba(196, 184, 150, 0.6);
    font-size: 1.5rem;
    cursor: pointer;
    line-height: 1;
    padding: 0;
}

.close-chat-btn:hover {
    color: #c9a84c;
}

.chat-messages {
    flex: 1;
    padding: 1.5rem;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.chat-message {
    display: flex;
    flex-direction: column;
    max-width: 85%;
    padding: 0.8rem 1rem;
    border-radius: 12px;
    font-size: 0.85rem;
    line-height: 1.4;
}

.chat-message.self {
    align-self: flex-end;
    background: rgba(201, 168, 76, 0.15);
    border: 1px solid rgba(201, 168, 76, 0.3);
    color: #f0e8d0;
    border-bottom-right-radius: 2px;
}

.chat-message.other {
    align-self: flex-start;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    color: #e0d0b0;
    border-bottom-left-radius: 2px;
}

.message-sender {
    font-size: 0.65rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 0.2rem;
    color: #c9a84c;
}

.chat-message.self .message-sender {
    color: rgba(196, 184, 150, 0.7);
}

.system-message {
    align-self: center;
    font-size: 0.7rem;
    color: rgba(196, 184, 150, 0.4);
    text-align: center;
    padding: 0.5rem;
    border-radius: 6px;
    background: rgba(201, 168, 76, 0.03);
    border: 1px dashed rgba(201, 168, 76, 0.1);
}

.chat-form {
    padding: 1.2rem;
    border-top: 1px solid rgba(201, 168, 76, 0.1);
    display: flex;
    gap: 0.6rem;
}

#chat-input {
    flex: 1;
    background: #0a0805;
    border: 1px solid rgba(201, 168, 76, 0.2);
    border-radius: 8px;
    padding: 0.7rem;
    color: #f0e8d0;
    font-size: 0.85rem;
}

#chat-input:focus {
    outline: none;
    border-color: #c9a84c;
}

#chat-send-btn {
    background: #c9a84c;
    color: #0a0805;
    border: none;
    border-radius: 8px;
    padding: 0.7rem 1.2rem;
    font-weight: 600;
    font-size: 0.85rem;
    cursor: pointer;
    transition: background 0.2s ease;
}

#chat-send-btn:hover {
    background: #e0c06a;
}

@media (max-width: 768px) {
    .room-container.video-mode {
        grid-template-columns: 1fr;
        padding: 2rem 1.5rem;
    }
}

@media (max-width: 480px) {
    .chat-drawer {
        width: 100%;
        right: -100%;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', async function() {
    const livekitUrl = <?= json_encode($livekitUrl) ?>;
    const token = <?= json_encode($livekitToken) ?>;
    const callUuid = <?= json_encode($call['uuid']) ?>;
    const isPerformer = <?= json_encode($isPerformer) ?>;
    const isAdmin = <?= json_encode($isAdmin ?? false) ?>;
    const callType = <?= json_encode($call['type']) ?>;
    const ratePerMin = parseFloat(<?= json_encode($call['rate_per_minute']) ?>);
    const redirectUrl = isAdmin
        ? window.location.origin + '<?= BASE_PATH ?>/admin/call'
        : (isPerformer
            ? window.location.origin + '<?= BASE_PATH ?>/performer-dash'
            : window.location.origin + '<?= BASE_PATH ?>/performer/<?= $performer['slug'] ?>');

    let room = null;
    let callDuration = 0;
    let durationInterval = null;
    let billingInterval = null;
    let audioContext = null;
    let analyser = null;
    let dataArray = null;

    const muteBtn = document.getElementById('mute-btn');
    const cameraBtn = document.getElementById('camera-btn');
    const chatBtn = document.getElementById('chat-btn');
    const closeChatBtn = document.getElementById('close-chat-btn');
    const chatDrawer = document.getElementById('chat-drawer');
    const chatForm = document.getElementById('chat-form');
    const chatInput = document.getElementById('chat-input');
    const chatMessages = document.getElementById('chat-messages');
    
    const hangupBtn = document.getElementById('hangup-btn');
    const statusLabel = document.getElementById('call-status-label');
    const durationLabel = document.getElementById('duration');
    const balanceLabel = document.getElementById('credit-balance');

    // Initialize WebRTC connection
    try {
        room = new LivekitClient.Room({
            adaptiveStream: true,
            dynacast: true,
        });

        // Set status
        statusLabel.innerText = "Connecting WebRTC...";

        // Handle participant events
        room.on(LivekitClient.RoomEvent.TrackSubscribed, (track, publication, participant) => {
            if (track.kind === 'audio') {
                const audioEl = track.attach();
                audioEl.volume = 1.0;
                audioEl.setAttribute('autoplay', '');
                audioEl.setAttribute('playsinline', '');
                document.body.appendChild(audioEl);

                // Force play — required for iOS Safari and Android Chrome autoplay policy
                const playPromise = audioEl.play();
                if (playPromise !== undefined) {
                    playPromise.catch(err => {
                        console.warn('Autoplay blocked, will play on next user gesture:', err);
                        const resumeAudio = () => { audioEl.play(); document.removeEventListener('click', resumeAudio); };
                        document.addEventListener('click', resumeAudio, { once: true });
                    });
                }

                statusLabel.innerText = 'Call Connected';
                startCallTimer();

                // Visualize using LOCAL stream so it works cross-browser
                if (room.localParticipant.audioTrackPublications.size > 0) {
                    const localPub = [...room.localParticipant.audioTrackPublications.values()][0];
                    if (localPub?.track?.mediaStream) {
                        setupVisualizer(localPub.track.mediaStream);
                    }
                }
            } else if (track.kind === 'video') {
                // Remote participant's video track!
                const remoteVideoEl = document.getElementById('remote-video');
                const placeholder = document.getElementById('video-placeholder');
                if (remoteVideoEl) {
                    track.attach(remoteVideoEl);
                    remoteVideoEl.style.display = 'block';
                }
                if (placeholder) {
                    placeholder.style.display = 'none';
                }

                statusLabel.innerText = 'Call Connected';
                startCallTimer();
            }
        });

        room.on(LivekitClient.RoomEvent.TrackUnsubscribed, (track, publication, participant) => {
            if (track.kind === 'video') {
                const remoteVideoEl = document.getElementById('remote-video');
                const placeholder = document.getElementById('video-placeholder');
                if (remoteVideoEl) {
                    track.detach(remoteVideoEl);
                    remoteVideoEl.style.display = 'none';
                }
                if (placeholder) {
                    placeholder.style.display = 'flex';
                }
            }
        });

        room.on(LivekitClient.RoomEvent.TrackMuted, (publication, participant) => {
            if (publication.kind === 'video') {
                const remoteVideoEl = document.getElementById('remote-video');
                const placeholder = document.getElementById('video-placeholder');
                if (remoteVideoEl) {
                    remoteVideoEl.style.display = 'none';
                }
                if (placeholder) {
                    placeholder.style.display = 'flex';
                }
            }
        });

        room.on(LivekitClient.RoomEvent.TrackUnmuted, (publication, participant) => {
            if (publication.kind === 'video') {
                const remoteVideoEl = document.getElementById('remote-video');
                const placeholder = document.getElementById('video-placeholder');
                if (remoteVideoEl) {
                    remoteVideoEl.style.display = 'block';
                }
                if (placeholder) {
                    placeholder.style.display = 'none';
                }
            }
        });

        room.on(LivekitClient.RoomEvent.ParticipantDisconnected, () => {
            statusLabel.innerText = "User disconnected.";
            setTimeout(disconnectCall, 2000);
        });

        room.on(LivekitClient.RoomEvent.Disconnected, () => {
            disconnectCall();
        });

        // Connect to LiveKit server
        console.log("Connecting to LiveKit room:", callUuid);
        await room.connect(livekitUrl, token);
        statusLabel.innerText = "Waiting for other participant...";
        console.log("Connected. Room participants count:", room.remoteParticipants.size);

        // Publish local microphone
        console.log("Publishing local microphone...");
        await room.localParticipant.setMicrophoneEnabled(true);
        console.log("Local microphone published successfully.");

        // Publish local camera if video call
        if (callType === 'video') {
            console.log("Publishing local camera...");
            await room.localParticipant.setCameraEnabled(true);
            
            // Handle local camera track to display in PIP
            const setPipTrack = () => {
                if (room.localParticipant.videoTrackPublications.size > 0) {
                    const videoPub = [...room.localParticipant.videoTrackPublications.values()][0];
                    if (videoPub?.track) {
                        const localVideoEl = document.getElementById('local-video');
                        if (localVideoEl) {
                            videoPub.track.attach(localVideoEl);
                        }
                    }
                }
            };
            setPipTrack();
            // Fallback in case track publication is slow
            setTimeout(setPipTrack, 1000);
        }

        // Start billing ticks for standard user client
        if (!isPerformer) {
            startBillingTicks();
        }

    } catch (error) {
        console.error("LiveKit connection error:", error);
        statusLabel.innerText = "Connection failed: " + error.message;
        statusLabel.style.color = "#a0243c";
    }

    // Call Duration timer
    function startCallTimer() {
        if (durationInterval) return;
        durationInterval = setInterval(() => {
            callDuration++;
            const minutes = String(Math.floor(callDuration / 60)).padStart(2, '0');
            const seconds = String(callDuration % 60).padStart(2, '0');
            durationLabel.innerText = `${minutes}:${seconds}`;
        }, 1000);
    }

    // Per-minute billing ticks
    function startBillingTicks() {
        tickBilling();
        billingInterval = setInterval(tickBilling, 60000);
    }

    function tickBilling() {
        const formData = new FormData();
        formData.append('call_uuid', callUuid);

        fetch('<?= BASE_PATH ?>/call/billing-tick', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                if (balanceLabel) {
                    balanceLabel.innerText = `Balance: ${parseFloat(data.new_balance).toFixed(2)} cr`;
                }
            } else {
                if (data.message === 'out_of_credits') {
                    alert("Your credit balance has been depleted. Disconnecting call.");
                }
                disconnectCall();
            }
        })
        .catch(err => {
            console.error("Billing tick error:", err);
        });
    }

    // Microphones Visualizer
    function setupVisualizer(stream) {
        try {
            const bars = document.querySelectorAll('.wave-bar');
            if (bars.length === 0) return;

            audioContext = new (window.AudioContext || window.webkitAudioContext)();
            const source = audioContext.createMediaStreamSource(stream);
            analyser = audioContext.createAnalyser();
            analyser.fftSize = 32;
            source.connect(analyser);
            
            const bufferLength = analyser.frequencyBinCount;
            dataArray = new Uint8Array(bufferLength);
            
            function draw() {
                if (!analyser) return;
                requestAnimationFrame(draw);
                analyser.getByteFrequencyData(dataArray);
                
                bars.forEach((bar, index) => {
                    const value = dataArray[index] || 0;
                    const percent = (value / 255) * 40;
                    bar.style.height = `${Math.max(5, percent)}px`;
                });
            }
            draw();
        } catch (e) {
            console.error(e);
        }
    }

    let callEnded = false;
    function disconnectCall() {
        if (callEnded) return;
        callEnded = true;

        if (durationInterval) clearInterval(durationInterval);
        if (billingInterval) clearInterval(billingInterval);

        if (room) {
            room.disconnect();
        }

        const endUrl = window.location.origin + '<?= BASE_PATH ?>/call/end/' + callUuid;
        const payload = new FormData();
        if (navigator.sendBeacon) {
            navigator.sendBeacon(endUrl, payload);
            window.location.href = redirectUrl;
        } else {
            fetch(endUrl, { method: 'POST', body: payload })
                .finally(() => { window.location.href = redirectUrl; });
        }
    }

    // Mute/Unmute microphone
    muteBtn.addEventListener('click', async () => {
        const isMuted = room.localParticipant.isMicrophoneEnabled === false;
        await room.localParticipant.setMicrophoneEnabled(isMuted);
        muteBtn.classList.toggle('active', !isMuted);
    });

    // Toggle Camera
    if (cameraBtn) {
        cameraBtn.addEventListener('click', async () => {
            const isCamEnabled = room.localParticipant.isCameraEnabled;
            await room.localParticipant.setCameraEnabled(!isCamEnabled);
            cameraBtn.classList.toggle('active', !isCamEnabled);

            const localVideoEl = document.getElementById('local-video');
            if (!isCamEnabled) {
                // If turning it on, reattach
                setTimeout(() => {
                    if (room.localParticipant.videoTrackPublications.size > 0) {
                        const videoPub = [...room.localParticipant.videoTrackPublications.values()][0];
                        if (videoPub?.track && localVideoEl) {
                            videoPub.track.attach(localVideoEl);
                        }
                    }
                }, 500);
            }
        });
    }

    // Toggle Chat Panel
    if (chatBtn && chatDrawer) {
        chatBtn.addEventListener('click', () => {
            chatDrawer.classList.toggle('open');
            if (chatDrawer.classList.contains('open')) {
                const badge = document.getElementById('chat-badge');
                if (badge) badge.style.display = 'none';
            }
        });
    }
    if (closeChatBtn && chatDrawer) {
        closeChatBtn.addEventListener('click', () => {
            chatDrawer.classList.remove('open');
        });
    }

    // Chat Form Submit
    if (chatForm && chatInput) {
        chatForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const text = chatInput.value.trim();
            if (!text) return;

            chatInput.value = '';

            try {
                const encoder = new TextEncoder();
                const data = encoder.encode(JSON.stringify({
                    text: text,
                    sender: isPerformer ? 'Performer' : 'Client'
                }));
                
                await room.localParticipant.publishData(data, { reliable: true });

                appendChatMessage('self', isPerformer ? 'Performer' : 'Client', text);
            } catch (err) {
                console.error("Failed to send message:", err);
            }
        });
    }

    // Receive Message via Data Channel
    room.on(LivekitClient.RoomEvent.DataReceived, (payload, participant) => {
        try {
            const decoder = new TextDecoder();
            const data = JSON.parse(decoder.decode(payload));
            if (data.text && data.sender) {
                appendChatMessage('other', data.sender, data.text);
                
                // Show badge if chat drawer is closed
                if (chatDrawer && !chatDrawer.classList.contains('open')) {
                    const badge = document.getElementById('chat-badge');
                    if (badge) badge.style.display = 'block';
                }
            }
        } catch (err) {
            console.error("Failed to decode data channel message:", err);
        }
    });

    function appendChatMessage(type, sender, text) {
        if (!chatMessages) return;
        const msgDiv = document.createElement('div');
        msgDiv.className = `chat-message ${type}`;
        
        const senderSpan = document.createElement('span');
        senderSpan.className = 'message-sender';
        senderSpan.innerText = sender;
        
        const textSpan = document.createElement('span');
        textSpan.innerText = text;
        
        msgDiv.appendChild(senderSpan);
        msgDiv.appendChild(textSpan);
        chatMessages.appendChild(msgDiv);
        
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    // Fullscreen Toggle logic
    const fullscreenBtn = document.getElementById('fullscreen-btn');
    const videoWrapper = document.querySelector('.video-grid-wrapper');

    if (fullscreenBtn && videoWrapper) {
        fullscreenBtn.addEventListener('click', () => {
            if (!document.fullscreenElement) {
                videoWrapper.requestFullscreen().catch(err => {
                    console.error("Error attempting to enable fullscreen:", err);
                });
            } else {
                document.exitFullscreen();
            }
        });
    }

    // Monitor fullscreen changes
    document.addEventListener('fullscreenchange', () => {
        if (document.fullscreenElement === videoWrapper) {
            fullscreenBtn.classList.add('active');
        } else {
            fullscreenBtn.classList.remove('active');
        }
    });

    // Hangup Call
    hangupBtn.addEventListener('click', () => {
        if (confirm("Are you sure you want to end the call?")) {
            disconnectCall();
        }
    });
});
</script>
