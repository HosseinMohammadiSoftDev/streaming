<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video Stream</title>
    <link href="https://vjs.zencdn.net/7.11.4/video-js.css" rel="stylesheet" />
</head>
<body>
    {{ $url }}
    <div class="container">
        <video id="my-video" class="video-js" controls preload="auto" width="640" height="264" data-setup="{}">
            <source src="{{ $url }}" type="application/x-mpegURL">
            Your browser does not support the video tag.
        </video>
    </div>
    <script src="https://vjs.zencdn.net/7.11.4/video.js"></script>
</body>
</html>
