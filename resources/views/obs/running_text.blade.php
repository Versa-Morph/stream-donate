<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Running Text</title>
    @php
        $enabled = $widget['enabled'] ?? false;
        $streamerMessage = isset($streamerMessage) ? $streamerMessage : 'Terima kasih atas donasi Anda! Semangat terus streamnya!';
        $speed = $widget['speed'] ?? '50';
        $direction = $widget['direction'] ?? 'left';
        $bg = $widget['bg'] ?? 'rgba(8,8,12,0.9)';
        $border = $widget['border'] ?? 'rgba(124,108,252,0.2)';
        $brand = $widget['brand'] ?? '#7c6cfc';
        $textColor = $widget['text_color'] ?? '#ffffff';
        $fontSize = $widget['font_size'] ?? '18';
        $fontFamily = $widget['font_family'] ?? 'inter';
        $radius = $widget['radius'] ?? '0';
        $opacity = $widget['opacity'] ?? '90';
    @endphp
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        html,body{
            background:transparent;
            overflow:hidden;
            width:100vw;
            height:100vh;
        }
        .running-text-widget{
            width:100%;height:100%;
            background:{{ $bg }};
            border-top:1px solid {{ $border }};
            border-bottom:1px solid {{ $border }};
            display:flex;
            align-items:center;
            overflow:hidden;
            position:relative;
        }
        .running-text-container{
            display:flex;
            align-items:center;
            white-space:nowrap;
            animation: scroll-{{ $direction }} {{ $speed }}s linear infinite;
        }
        .running-text-item{
            font-family:'{{ $fontFamily }}',-apple-system,BlinkMacSystemFont,sans-serif;
            font-size:{{ $fontSize }}px;
            font-weight:600;
            color:{{ $textColor }};
            padding:0 50px;
            display:inline-flex;
            align-items:center;
            gap:10px;
        }
        .running-text-icon{
            color:{{ $brand }};
            font-size:calc({{ $fontSize }}px + 4px);
        }
        @keyframes scroll-left{
            0% { transform: translateX(100%); }
            100% { transform: translateX(-100%); }
        }
        @keyframes scroll-right{
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }
    </style>
</head>
<body>
    @if($enabled)
    <div class="running-text-widget">
        <div class="running-text-container" id="running-text">
            <span class="running-text-item">
                <span class="running-text-icon">📢</span>
                {{ $streamerMessage }}
            </span>
            @foreach($donations as $donation)
            <span class="running-text-item">
                <span class="running-text-icon">{{ $donation->emoji ?? '💖' }}</span>
                {{ $donation->name }}: {{ $donation->message }}
            </span>
            @endforeach
        </div>
    </div>
    @else
    <div class="running-text-widget">
        <div style="color:rgba(255,255,255,0.5);font-family:inter,sans-serif;font-size:14px;padding:0 20px">
            Running Text nonaktif
        </div>
    </div>
    @endif
</body>
</html>