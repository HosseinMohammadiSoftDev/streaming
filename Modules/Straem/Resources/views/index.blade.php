<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video Stream</title>
    <link href="https://vjs.zencdn.net/7.11.4/video-js.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/video.js/7.11.4/video.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
</head>
<body>
        {{ $url }}
    <div class="container">
        <video id="my-video" class="video-js" controls preload="auto" width="640" height="264" data-setup="{}">
            <source src="{{ $url }}" type="application/x-mpegURL">
            Your browser does not support the video tag.
        </video>
    </div>
    
    <script>
        var player = videojs('my-video');

        if (Hls.isSupported()) {
            var video = document.getElementById('my-video');
            var hls = new Hls();
            hls.loadSource('{{ $url }}');
            hls.attachMedia(video);
            hls.on(Hls.Events.MANIFEST_PARSED, function() {
                video.play();
            });
        } else if (videojs.canPlayType('application/vnd.apple.mpegurl')) {
            var video = document.getElementById('my-video');
            video.src = '{{ $url }}';
            video.addEventListener('canplay', function() {
                video.play();
            });
        }
    </script>
</body>
</html>
