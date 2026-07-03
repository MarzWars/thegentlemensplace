<?php
// app/Views/calls/calling.php
?>
<div class="calling-wrapper">
    <div class="calling-container">
        <div class="avatar-container">
            <div class="pulse-ring"></div>
            <div class="pulse-ring-2"></div>
            <?php if (!empty($performer['profile_photo'])): ?>
                <img class="performer-avatar" src="<?= BASE_PATH ?>/<?= htmlspecialchars($performer['profile_photo']) ?>" alt="<?= htmlspecialchars($performer['display_name']) ?>">
            <?php else: ?>
                <div class="avatar-placeholder"><?= strtoupper(substr($performer['display_name'], 0, 1)) ?></div>
            <?php endif; ?>
        </div>

        <h1 class="calling-title">Connecting with <?= htmlspecialchars($performer['display_name']) ?></h1>
        <p class="calling-subtitle">Waiting for performer to accept the call...</p>

        <div class="status-indicator">
            <span class="dot-flashing"></span>
            <span id="status-text">Ringing...</span>
        </div>

        <div class="actions">
            <button id="cancel-btn" class="btn btn-decline">Cancel Call</button>
        </div>
    </div>
</div>

<style>
.calling-wrapper {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: calc(100vh - 120px);
    background: radial-gradient(circle at center, #1a150e 0%, #0a0805 100%);
    color: #f0e8d0;
    font-family: 'Outfit', sans-serif;
}

.calling-container {
    text-align: center;
    background: rgba(17, 16, 8, 0.6);
    border: 1px solid rgba(201, 168, 76, 0.15);
    padding: 3rem;
    border-radius: 24px;
    backdrop-filter: blur(16px);
    box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5);
    max-width: 480px;
    width: 90%;
}

.avatar-container {
    position: relative;
    width: 140px;
    height: 140px;
    margin: 0 auto 2.5rem;
}

.performer-avatar, .avatar-placeholder {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #c9a84c;
    position: relative;
    z-index: 3;
    background: #111008;
    box-shadow: 0 10px 25px rgba(201, 168, 76, 0.2);
}

.avatar-placeholder {
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 3rem;
    font-weight: 700;
    color: #c9a84c;
}

.pulse-ring, .pulse-ring-2 {
    position: absolute;
    top: -5px;
    left: -5px;
    right: -5px;
    bottom: -5px;
    border-radius: 50%;
    border: 1px solid rgba(201, 168, 76, 0.4);
    z-index: 1;
    animation: calling-pulse 2s cubic-bezier(0.215, 0.61, 0.355, 1) infinite;
}

.pulse-ring-2 {
    animation-delay: 0.6s;
}

@keyframes calling-pulse {
    0% {
        transform: scale(0.95);
        opacity: 0.8;
    }
    100% {
        transform: scale(1.6);
        opacity: 0;
    }
}

.calling-title {
    font-size: 1.8rem;
    font-weight: 600;
    color: #f0e8d0;
    margin-bottom: 0.5rem;
    letter-spacing: -0.02em;
}

.calling-subtitle {
    font-size: 0.95rem;
    color: rgba(196, 184, 150, 0.6);
    margin-bottom: 2rem;
}

.status-indicator {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    margin-bottom: 3rem;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.15em;
    color: #c9a84c;
}

.dot-flashing {
    position: relative;
    width: 8px;
    height: 8px;
    border-radius: 5px;
    background-color: #c9a84c;
    color: #c9a84c;
    animation: dot-flashing 1s infinite linear alternate;
    animation-delay: .5s;
}

.dot-flashing::before, .dot-flashing::after {
    content: '';
    display: inline-block;
    position: absolute;
    top: 0;
    width: 8px;
    height: 8px;
    border-radius: 5px;
    background-color: #c9a84c;
    color: #c9a84c;
}

.dot-flashing::before {
    left: -14px;
    animation: dot-flashing 1s infinite alternate;
    animation-delay: 0s;
}

.dot-flashing::after {
    left: 14px;
    animation: dot-flashing 1s infinite alternate;
    animation-delay: 1s;
}

@keyframes dot-flashing {
    0% {
        background-color: #c9a84c;
    }
    50%, 100% {
        background-color: rgba(201, 168, 76, 0.2);
    }
}

.btn {
    padding: 0.9rem 2.5rem;
    font-size: 0.8rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.15em;
    border-radius: 50px;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-decline {
    background: #6b1a2a;
    border: 1px solid rgba(160, 36, 60, 0.4);
    color: #f0b0b8;
}

.btn-decline:hover {
    background: #a0243c;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(160, 36, 60, 0.3);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const callUuid = <?= json_encode($call['uuid']) ?>;
    const statusUrl = window.location.origin + '<?= BASE_PATH ?>/call/status/' + callUuid;
    const roomUrl = window.location.origin + '<?= BASE_PATH ?>/call/room/' + callUuid;
    const cancelUrl = window.location.origin + '<?= BASE_PATH ?>/performer/<?= $performer['slug'] ?>';
    
    // Synthesize telephone ringing sound using Web Audio API
    let audioCtx = null;
    let ringInterval = null;

    function playRingSound() {
        if (!audioCtx) {
            audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        }
        
        // Ringing cadence: 2 seconds on, 4 seconds off
        function ring() {
            let osc1 = audioCtx.createOscillator();
            let osc2 = audioCtx.createOscillator();
            let gainNode = audioCtx.createGain();

            osc1.type = 'sine';
            osc1.frequency.setValueAtTime(440, audioCtx.currentTime); // Standard ring frequency 1
            
            osc2.type = 'sine';
            osc2.frequency.setValueAtTime(480, audioCtx.currentTime); // Standard ring frequency 2

            gainNode.gain.setValueAtTime(0, audioCtx.currentTime);
            gainNode.gain.linearRampToValueAtTime(0.2, audioCtx.currentTime + 0.1);
            gainNode.gain.setValueAtTime(0.2, audioCtx.currentTime + 1.8);
            gainNode.gain.linearRampToValueAtTime(0, audioCtx.currentTime + 2.0);

            osc1.connect(gainNode);
            osc2.connect(gainNode);
            gainNode.connect(audioCtx.destination);

            osc1.start();
            osc2.start();

            osc1.stop(audioCtx.currentTime + 2.0);
            osc2.stop(audioCtx.currentTime + 2.0);
        }

        ring();
        ringInterval = setInterval(ring, 6000);
    }

    // Try playing sound on user interaction or after a small delay
    document.body.addEventListener('click', function() {
        if (!ringInterval) playRingSound();
    }, { once: true });

    setTimeout(() => {
        if (!ringInterval) {
            try { playRingSound(); } catch (e) {}
        }
    }, 1000);

    // Poll status of the call
    const pollInterval = setInterval(function() {
        fetch(statusUrl + '?t=' + Date.now())
            .then(res => res.json())
            .then(data => {
                if (data.status === 'accepted' || data.status === 'in_progress') {
                    clearInterval(pollInterval);
                    if (ringInterval) clearInterval(ringInterval);
                    document.getElementById('status-text').innerText = 'Accepted — Connecting...';
                    document.querySelector('.calling-subtitle').innerText = 'Joining voice room...';
                    window.location.href = roomUrl;
                } else if (data.status === 'declined' || data.status === 'failed') {
                    clearInterval(pollInterval);
                    if (ringInterval) clearInterval(ringInterval);
                    document.getElementById('status-text').innerText = 'Call Declined';
                    alert('The performer declined the call or is busy.');
                    window.location.href = cancelUrl;
                }
            })
            .catch(err => console.error('Error polling call status:', err));
    }, 2000);

    document.getElementById('cancel-btn').addEventListener('click', function() {
        clearInterval(pollInterval);
        if (ringInterval) clearInterval(ringInterval);
        window.location.href = cancelUrl;
    });
});
</script>
