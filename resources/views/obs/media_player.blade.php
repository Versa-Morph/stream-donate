<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>StreamDonate — OBS Media Player</title>
    <style>
        /* ─── BASE ─── */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body {
            width: 1920px; 
            height: 1080px;
            background: transparent !important;
            overflow: hidden;
            -webkit-font-smoothing: antialiased;
        }

        /* ─── MEDIA CONTAINER ─── */
        #media-container {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.5s ease;
        }

        #media-container.visible {
            opacity: 1;
        }

        /* ─── VIDEO/AUDIO PLAYER ─── */
        #media-player {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        /* ─── STATUS INDICATOR ─── */
        #status {
            position: fixed;
            bottom: 12px;
            right: 12px;
            font-family: 'Courier New', monospace;
            font-size: 11px;
            color: rgba(255,255,255,.25);
            z-index: 9999;
            background: rgba(0,0,0,.4);
            padding: 4px 10px;
            border-radius: 12px;
        }
    </style>
</head>
<body>
    {{-- Status indicator --}}
    <div id="status">● connecting…</div>

    {{-- Media container --}}
    <div id="media-container">
        <video id="media-player" preload="auto"></video>
    </div>

    <script>
    // ─── Configuration ───
    const STREAMER_SLUG = @json($streamer->slug);
    const API_KEY = @json($apiKey);
    
    const statusEl = document.getElementById('status');
    const mediaContainer = document.getElementById('media-container');
    const mediaPlayer = document.getElementById('media-player');

    // ─── SSE Connection ───
    let currentEventSource = null;
    let sseHandlers = {};
    const seenIds = new Set();
    const queue = [];
    let isPlaying = false;

    function buildSseUrl() {
        const base = `/${STREAMER_SLUG}/sse?key=${API_KEY}`;
        return base;
    }

    function connectSSE() {
        // Clean up existing connection
        if (currentEventSource) {
            if (sseHandlers.donation) currentEventSource.removeEventListener('donation', sseHandlers.donation);
            if (sseHandlers.ping) currentEventSource.removeEventListener('ping', sseHandlers.ping);
            if (sseHandlers.stream_error) currentEventSource.removeEventListener('stream_error', sseHandlers.stream_error);
            currentEventSource.close();
            currentEventSource = null;
        }

        currentEventSource = new EventSource(buildSseUrl());
        
        sseHandlers.onopen = function() {
            statusEl.textContent = '● live';
            statusEl.style.color = 'rgba(34,211,160,.4)';
        };
        currentEventSource.onopen = sseHandlers.onopen;

        sseHandlers.donation = function(e) {
            try {
                const d = JSON.parse(e.data);
                
                // Check if donation has media file
                if (d.media_file) {
                    if (seenIds.has(d.seq ?? d.id)) return;
                    seenIds.add(d.seq ?? d.id);
                    addToQueue(d);
                }
            } catch(err) { 
                console.error('SSE parse error:', err); 
            }
        };
        currentEventSource.addEventListener('donation', sseHandlers.donation);

        sseHandlers.ping = function() {};
        currentEventSource.addEventListener('ping', sseHandlers.ping);

        sseHandlers.stream_error = function(e) {
            statusEl.textContent = '● error — reconnecting…';
            statusEl.style.color = 'rgba(249,115,22,.4)';
            if (currentEventSource) currentEventSource.close();
            setTimeout(connectSSE, 5000);
        };
        currentEventSource.addEventListener('stream_error', sseHandlers.stream_error);

        sseHandlers.onerror = function() {
            statusEl.textContent = '● reconnecting…';
            statusEl.style.color = 'rgba(249,115,22,.4)';
            if (currentEventSource) currentEventSource.close();
            setTimeout(connectSSE, 3000);
        };
        currentEventSource.onerror = sseHandlers.onerror;
    }
    connectSSE();

    // ─── Queue Management ───
    function addToQueue(donation) {
        queue.push(donation);
        if (!isPlaying) processQueue();
    }

    function processQueue() {
        if (queue.length === 0) {
            isPlaying = false;
            return;
        }
        isPlaying = true;
        playMedia(queue.shift());
    }

    // ─── Media Playback ───
    function playMedia(donation) {
        const mediaUrl = `/storage/${donation.media_file}`;
        const mediaType = donation.media_type; // 'video' or 'audio'
        
        console.log('Playing media:', mediaUrl, 'Type:', mediaType);

        // Show container
        mediaContainer.classList.add('visible');

        // Set media source
        mediaPlayer.src = mediaUrl;

        // Handle video vs audio
        if (mediaType === 'video') {
            mediaPlayer.style.display = 'block';
        } else {
            // For audio, hide the player but still play it
            mediaPlayer.style.display = 'none';
        }

        // Play media
        mediaPlayer.play().catch(err => {
            console.error('Error playing media:', err);
            onMediaEnd();
        });

        // Handle media end
        mediaPlayer.onended = onMediaEnd;
    }

    function onMediaEnd() {
        // Hide container
        mediaContainer.classList.remove('visible');
        
        // Clear source
        mediaPlayer.src = '';
        mediaPlayer.onended = null;
        
        // Process next in queue
        isPlaying = false;
        setTimeout(processQueue, 500);
    }

    // ─── Error Handling ───
    mediaPlayer.onerror = function(e) {
        console.error('Media player error:', e);
        onMediaEnd();
    };
    </script>
</body>
</html>
