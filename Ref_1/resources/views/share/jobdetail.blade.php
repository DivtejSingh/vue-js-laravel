<?php
$app_url = 'https://bizlx.itechvision.in';
$id = $id;
$url = $app_url.'/api/jobcategory/job-detail/'.$id.'/1'; // path to your JSON file
$data = file_get_contents($url); // put the contents of the file into a variable
$job = json_decode($data); // decode the JSON feed
$redirecturl = 'https://www.bizapp.itechvision.in/jobs/detail/'.$id;
?>
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Job, {{$job->jobdetails->job_title}} - Salary From  Rs{{$job->jobdetails->salary_from}} - by {{$job->jobdetails->name}} - Bizlx</title>
    <meta name="description" content="{{$job->jobdetails->job_title}}- {{$job->jobdetails->job_description}} - Salary From  Rs{{$job->jobdetails->salary_from}}">
    <meta property="og:url" content="https://www.bizapp.itechvision.in/jobs/detail/<?php echo $id;?>">
    <meta property="og:title" content="Job, {{$job->jobdetails->job_title}} - Salary From  Rs{{$job->jobdetails->salary_from}} - by {{$job->jobdetails->name}} Bizlx">

    <meta property="og:image"
    content="<?php if( $job->jobdetails->image_url == null ){
        echo 'https://dummyimage.com/1600x900/1976d2/ffffff.jpg&text='.$job->jobdetails->job_title;
        }else{
            echo $app_url.$job->jobdetails->image_url;
        }?>">

    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">

    <meta property="og:description" content="{{$job->jobdetails->job_title}}- {{$job->jobdetails->job_description}} - Salary From  Rs{{$job->jobdetails->salary_from}}">
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

