
<?php
$app_url = 'https://bizlx.itechvision.in';
$id = $id;
$url = $app_url.'/api/video/detail/'.$id.'/1'; // path to your JSON file
$data = file_get_contents($url); // put the contents of the file into a variable
$video = json_decode($data); // decode the JSON feed
$redirecturl = 'https://www.bizapp.itechvision.in/video/detail/'.$id;
?>
    <!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{$video->videoProfile->video_title}} - by {{$video->videoProfile->name}} - Bizlx</title>
    <meta name="description" content="{{$video->videoProfile->video_title}}">
    <meta property="og:url" content="https://www.bizapp.itechvision.in/video/detail/<?php echo $id;?>">
    <meta property="og:title" content="{{$video->videoProfile->video_title}} - by {{$video->videoProfile->name}} Bizlx">
    <meta property="og:image" content="<?php echo 'https://dummyimage.com/1600x900/1976d2/ffffff.jpg&text='.$video->videoProfile->video_title?>">
    <meta property="og:video" content="<?php echo 'https://www.youtube.com/watch?v='.$video->videoProfile->youtube_id?>" />
    <meta property="og:description" content="{{$video->videoProfile->video_title}}- {{$video->videoProfile->video_description}}">
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="{{asset('css/app.css')}}" rel="stylesheet">
    <link href="{{asset('css/style.css.css')}}" rel="stylesheet">
    <meta http-equiv = "refresh" content = "0; url = <?php echo $redirecturl?>" />

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

</head>
<body class="js">

<div class="container">
    <div class="row justify-content-center my-5">
        <div class="col-6">
            <div class="card">
                <div class="card-header text-center">
                    <a target="_blank" class="fw-bold" href="{{$redirecturl}}">Main Site</a>
                </div>
                <div class="card-body text-center">
                    <img src="{{asset('images/logo.png')}}" alt="logo" class="img-fluid">
                </div>
            </div>
        </div>
    </div>
</div>
</body>

</html>

