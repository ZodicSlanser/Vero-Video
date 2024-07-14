<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interactive Video Quiz</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <video id="videoPlayer" width="640" height="360" controls>
        <source src="{{ asset('videos/video.mp4') }}" type="video/mp4">
        Your browser does not support the video tag.
    </video>
    <div id="quizOverlay" class="hidden">
        <p id="questionText"></p>
        <form id="quizForm">
            <div id="optionsContainer"></div>
            <button type="submit" id="submitAnswer" class="btn">Submit</button>
        </form>
    </div>
    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
