<?php

function apiResponse($address)
{
    $latitude = '';
    $longitude = '';
    $url = 'https://geokeo.com/geocode/v1/search.php?q='.urlencode($address).'&api=aabf1e32bb4f2321a163dc0f3ebae589';
    $curl = curl_init();
    curl_setopt_array($curl, array(
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET',
    ));
    $response = curl_exec($curl);
    curl_close($curl);
    $data = json_decode($response, TRUE);
    
    if(isset($data['results'])){
        for( $i = 0; $i < 1; $i++ ){
            $latitude = $data['results'][$i]['geometry']['viewport']['northeast']['lat'];
            $longitude = $data['results'][$i]['geometry']['viewport']['northeast']['lng'];
        }
    }
    
    return [
        'latitude' => $latitude,
        'longitude' => $longitude
    ];
}