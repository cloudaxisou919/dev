<?php

namespace MagicLensAI\Controllers;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Storage;

class TestController extends Controller
{
    function test(){
        $headers[] = 'Accept: image/gif, image/x-bitmap, image/jpeg, image/pjpeg';              
        $headers[] = 'Connection: Keep-Alive';         
        $headers[] = 'Content-type: application/x-www-form-urlencoded;charset=UTF-8';         
        $user_agent = 'php';  
        $url = 'https://cdn.midjourney.com/2213c311-9fa6-475d-826b-fdccf1f2fc07/0_1.png';      
        $process = curl_init($url);         
        curl_setopt($process, CURLOPT_HTTPHEADER, $headers);         
        curl_setopt($process, CURLOPT_HEADER, 0);         
        curl_setopt($process, CURLOPT_USERAGENT, $user_agent); //check here         
        curl_setopt($process, CURLOPT_TIMEOUT, 30);         
        curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);         
        curl_setopt($process, CURLOPT_FOLLOWLOCATION, 1);         
        $return = curl_exec($process);         
        curl_close($process);   
        echo '<pre>';print_r($return);exit;      
        // return $return;     
    // } 
    
    $imgurl = 'https://cdn.midjourney.com/2213c311-9fa6-475d-826b-fdccf1f2fc07/0_1.png'; 
    $imagename= basename($imgurl);
    // $image = getimg($imgurl); 
    file_put_contents('uploads/'.$imagename,$imgurl); 
    }

    function test1()
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://cdn.discordapp.com/attachments/1130491916455247892/1138356306294603916/KyJlu4_Boy_3de7f4f7-f680-48d9-a63b-9595345a7a17.png',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET',
          CURLOPT_HTTPHEADER => array(
            'Accept: application/json',
            'Accept-Charset: utf-8',
            'Authorization: Bearer 8c086293-daf3-43d8-ae80-9e3b28831fa6'
          ),
        ));
        
        $response = curl_exec($curl);
        
        curl_close($curl);
        // echo '<pre>';print_r($response);
        $filename = uniqid('image_') . '.png';

        Storage::disk('public')->put($filename,  $response );
    }
}
