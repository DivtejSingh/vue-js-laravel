
<?php
$app_url = 'https://bizlx.itechvision.in';
$id = $id;
$url = $app_url.'/api/deals/details/'.$id.'/1'; // path to your JSON file
$data = file_get_contents($url); // put the contents of the file into a variable
$deal = json_decode($data); // decode the JSON feed
$redirecturl = 'https://www.bizapp.itechvision.in/deals/detail/'.$id;
?>
    <!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{$deal->hotdeal->hot_deal_title}} Price Rs {{$deal->hotdeal->price_to}} - Bizlx</title>
    <meta name="description" content="{{$deal->hotdeal->hot_deal_description}} - Deal From Rs {{$deal->hotdeal->price_from}} to Rs {{$deal->hotdeal->price_to}}">
    <meta property="og:url" content="https://www.bizapp.itechvision.in/deal/datails/<?php echo $id;?>">
    <meta property="og:title" content="{{$deal->hotdeal->hot_deal_title}} Price Rs {{$deal->hotdeal->price_to}} - by {{$deal->hotdeal->name}} Bizlx">
    <meta property="og:image"
          content="<?php if($deal->hotdeal->images !== null){
              echo $app_url.$deal->hotdeal->images[0]->hotdeal_img_url;
          }
          else{
              echo 'https://dummyimage.com/1600x900/1976d2/ffffff.jpg&text='.$deal->hotdeal->username;
          }?>">
    <meta property="og:description" content="{{$deal->hotdeal->hot_deal_description}} - {{$deal->hotdeal->price_from}} to {{$deal->hotdeal->price_to}}">
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

