<?php
$app_url = 'https://bizlx.itechvision.in';
$id = $uname;
$url = 'https://bizlx.itechvision.in/api/businesses/profile/'.$id.'/1'; // path to your JSON file

$data = file_get_contents($url); // put the contents of the file into a variable
$buss = json_decode($data); // decode the JSON feed
$redirecturl = 'https://www.bizapp.itechvision.in/'.$id;
?>
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{$buss->businessprofile->name}} - BizLX</title>
    <meta property="og:url" content="https://www.bizapp.itechvision.in/">
    <meta property="og:title" content="{{$buss->businessprofile->name}} - Bizlx">
    <meta property="og:image"
          content="<?php if($buss->businessprofile->user_avatar !== null){
                            echo $app_url.$buss->coverimages[0]->image_url;
    }
    else{
        echo 'https://dummyimage.com/1600x900/1976d2/ffffff.jpg&text='.$buss->businessprofile->first_last_letter;
    }?>">
    <meta property="og:description" content="{{$buss->businessprofile->about}}">
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="{{asset('css/app.css')}}" rel="stylesheet">
    <link href="{{asset('css/style.css.css')}}" rel="stylesheet">
{{--    <meta http-equiv = "refresh" content = "0; url = <?php echo $redirecturl?>" />--}}

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
{{--    <script>--}}
{{--        $(document).ready(function () {--}}
{{--            var data--}}
{{--            $.ajax({--}}
{{--                dataType: 'json',--}}
{{--                url: 'api/businesses/profile/127/1',--}}
{{--                data: data,--}}
{{--                success: function (data) {--}}
{{--                    // begin accessing JSON data here--}}
{{--                    console.log(data)--}}
{{--                },--}}
{{--            })--}}
{{--        })--}}
{{--        // $(document).ready(function () {--}}
{{--        //     $.getJSON('api/businesses/profile/127/1', function (data) {--}}
{{--        //         // begin accessing JSON data here--}}
{{--        //         console.log(data)--}}
{{--        //     })--}}
{{--        // })--}}
{{--    </script>--}}
</head>
<body class="js">

<div class="container">
    <div class="row justify-content-center my-5">
        <div class="col-6">
            <div class="card">
                {{$buss->businessprofile->name}}
                <div class="card-header text-center">
                    <a target="_blank" class="fw-bold" href="https://master.d22ai1wes78a83.amplifyapp.com">Main Site</a>
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

