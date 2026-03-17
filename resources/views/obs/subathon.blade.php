<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subathon Timer</title>
    @php
        $bg = $widget['bg'] ?? 'rgba(8,8,12,0.95)';
        $border = $widget['border'] ?? 'rgba(124,108,252,0.25)';
        $brand = $widget['brand'] ?? '#7c6cfc';
        $brand2 = $widget['brand2'] ?? '#a99dff';
        $text = $widget['text'] ?? '#f1f1f6';
        $text2 = $widget['text2'] ?? '#a0a0b4';
        $radius = $widget['radius'] ?? '16';
        $width = $widget['width'] ?? '320';
        $showLabel = $widget['showLabel'] ?? true;
        $labelText = $widget['labelText'] ?? 'Sisa Waktu';
    @endphp
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{
            font-family:'Inter',-apple-system,BlinkMacSystemFont,sans-serif;
            background:transparent;
            overflow:hidden;
            width:100vw;
            height:100vh;
        }
        .subathon-widget{
            width:100%;height:100%;
            display:flex;flex-direction:column;
            align-items:center;justify-content:center;
            background:{{ $bg }};
            border:1px solid {{ $border }};
            border-radius:{{ $radius }}px;
            padding:20px;
            transition:all .3s ease;
        }
        .subathon-label{
            font-size:12px;font-weight:600;
            text-transform:uppercase;letter-spacing:1.5px;
            color:{{ $text2 }};
            margin-bottom:8px;
            opacity:{{ $showLabel ? 1 : 0 }};
        }
        .subathon-timer{
            font-family:'Space Grotesk',sans-serif;
            font-size:48px;font-weight:700;
            color:{{ $text }};
            line-height:1;
            letter-spacing:-1px;
        }
        .subathon-timer.warning{
            color:#f97316;
            animation:pulse 1s ease-in-out infinite;
        }
        .subathon-timer.danger{
            color:#ef4444;
            animation:pulse .5s ease-in-out infinite;
        }
        .subathon-bar{
            width:100%;max-width:240px;
            height:6px;
            background:rgba(255,255,255,0.1);
            border-radius:3px;
            margin-top:16px;
            overflow:hidden;
        }
        .subathon-bar-fill{
            height:100%;
            background:linear-gradient(90deg,{{ $brand }},{{ $brand2 }});
            border-radius:3px;
            transition:width 1s linear;
        }
        .subathon-bar-fill.low{
            background:linear-gradient(90deg,#f97316,#fb923c);
        }
        .subathon-bar-fill.critical{
            background:linear-gradient(90deg,#ef4444,#f87171);
        }
        .subathon-remaining{
            font-size:11px;color:{{ $text2 }};
            margin-top:8px;
        }
        @keyframes pulse{
            0%,100%{opacity:1}
            50%{opacity:.7}
        }
    </style>
</head>
<body>
    <div class="subathon-widget" id="widget">
        @if($showLabel)
        <div class="subathon-label">{{ $labelText }}</div>
        @endif
        <div class="subathon-timer" id="timer">00:00:00</div>
        <div class="subathon-bar">
            <div class="subathon-bar-fill" id="bar"></div>
        </div>
        <div class="subathon-remaining" id="remaining">Menghubungkan...</div>
    </div>

    <script>
        const SLUG = '{{ $streamer->slug }}';
        const API_KEY = '{{ $streamer->api_key }}';
        
        let currentMinutes = 0;
        let durationMinutes = 60;
        let enabled = false;
        let lastUpdate = Date.now();
        
        const timerEl = document.getElementById('timer');
        const barEl = document.getElementById('bar');
        const remainingEl = document.getElementById('remaining');
        const widgetEl = document.getElementById('widget');
        
        function formatTime(mins) {
            const h = Math.floor(mins / 60);
            const m = mins % 60;
            return `${String(h).padStart(2,'0')}:${String(m).padStart(2,'0')}:00`;
        }
        
        function updateDisplay() {
            timerEl.textContent = formatTime(currentMinutes);
            
            const pct = durationMinutes > 0 ? (currentMinutes / durationMinutes) * 100 : 0;
            barEl.style.width = pct + '%';
            
            if (pct <= 10) {
                timerEl.className = 'subathon-timer danger';
                barEl.className = 'subathon-bar-fill critical';
            } else if (pct <= 25) {
                timerEl.className = 'subathon-timer warning';
                barEl.className = 'subathon-bar-fill low';
            } else {
                timerEl.className = 'subathon-timer';
                barEl.className = 'subathon-bar-fill';
            }
            
            remainingEl.textContent = enabled 
                ? `${currentMinutes} menit tersisa`
                : 'Subathon nonaktif';
        }
        
        let currentEventSource = null;
        let sseHandlers = { stats: null, donation: null, stream_error: null, onerror: null };
        
        function connectSSE() {
            // Clean up existing connection and handlers
            if (currentEventSource) {
                if (sseHandlers.stats) currentEventSource.removeEventListener('stats', sseHandlers.stats);
                if (sseHandlers.donation) currentEventSource.removeEventListener('donation', sseHandlers.donation);
                if (sseHandlers.stream_error) currentEventSource.removeEventListener('stream_error', sseHandlers.stream_error);
                currentEventSource.close();
                currentEventSource = null;
            }

            const url = `/{{ $streamer->slug }}/sse?key=${API_KEY}&last_seq=0`;
            currentEventSource = new EventSource(url);
            
            sseHandlers.stats = (e) => {
                const data = JSON.parse(e.data);
                if (data.subathon) {
                    enabled = data.subathon.enabled;
                    currentMinutes = data.subathon.currentMinutes || 0;
                    durationMinutes = data.subathon.durationMinutes || 60;
                    lastUpdate = Date.now();
                    updateDisplay();
                }
            };
            currentEventSource.addEventListener('stats', sseHandlers.stats);
            
            sseHandlers.donation = (e) => {
                const donation = JSON.parse(e.data);
                // Subathon akan di-update via stats event
            };
            currentEventSource.addEventListener('donation', sseHandlers.donation);
            
            sseHandlers.stream_error = (e) => {
                console.error('SSE error:', e.data);
                if (currentEventSource) currentEventSource.close();
                setTimeout(connectSSE, 5000);
            };
            currentEventSource.addEventListener('stream_error', sseHandlers.stream_error);
            
            sseHandlers.onerror = () => {
                if (currentEventSource) currentEventSource.close();
                setTimeout(connectSSE, 5000);
            };
            currentEventSource.onerror = sseHandlers.onerror;
        }
        
        // Polling fallback setiap 5 detik untuk memastikan timer akurat
        setInterval(() => {
            if (!enabled) return;
            
            fetch(`/{{ $streamer->slug }}/stats?key=${API_KEY}`)
                .then(r => r.json())
                .then(data => {
                    if (data.subathon) {
                        currentMinutes = data.subathon.currentMinutes || 0;
                        durationMinutes = data.subathon.durationMinutes || 60;
                        lastUpdate = Date.now();
                        updateDisplay();
                    }
                })
                .catch(() => {});
        }, 5000);
        
        // Update setiap detik untuk countdown lokal (opsional visual)
        setInterval(() => {
            if (!enabled || currentMinutes <= 0) return;
            
            // Hitung mundur berdasarkan last update
            const elapsed = Math.floor((Date.now() - lastUpdate) / 1000);
            // Kita tidak menguranginya sendiri karena server yang jaga,
            // tapi ini bisa digunakan untuk animasi visual saja
        }, 1000);
        
        connectSSE();
    </script>
</body>
</html>