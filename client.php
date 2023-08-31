<?php
// Method: POST, PUT, GET etc
// Data: array("param" => "value") ==> index.php?param=value

function CallAPI($method, $url, $data) {
    $curl = curl_init();

    switch ($method) {
        case "POST":
            curl_setopt($curl, CURLOPT_POST, 1);
            if ($data)
                curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
            break;
        case "PUT":
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
            if ($data)
                curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
            break;
    }

    curl_setopt($curl, CURLOPT_URL, $url);

    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    $result = curl_exec($curl);

    @curl_setopt($curl, CURLOPT_HEADER  , true);  // we want headers
    @curl_setopt($curl, CURLOPT_NOBODY  , true);  // we don't need body
    $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

//    if(!$result) {
//        echo("Connection failure!");
//    }

    curl_close($curl);

    if($httpcode == "401"){
        echo "401";
    };
    return $result;
}

$result = CallAPI("POST", "https://area51.serverzone.dev/robot/", [
    'email' =>  "radek.jenik36@gmail.com"
]);
$data = json_decode($result);

$robotId = $data->id;

if($robotId){
    $moveUrl = "https://area51.serverzone.dev/robot/".$robotId."/move";

    echo "<br> MOVE URL: " . $moveUrl;

    $hallWidth = 0;
    $hallHeight = 0;

    $leftDistance = 0;
    $restDistance = 5;
    $attempt = 0;
    while($restDistance != 0 && $attempt < 20){
        $currentDistance = CallAPI("PUT", $moveUrl, [
            'direction' =>  "left",
            'distance' => 5
        ]);

        if(!empty($currentDistance)){
            echo "<br> - LEFT DISTANCE: " . $currentDistance;
            $temp = json_decode($currentDistance);
            $restDistance = $temp->distance;
            $leftDistance = $leftDistance + $restDistance;
        }else{
            $attempt = $attempt + 1;
        }
    }
    echo "<div>LEFT TOTAL: ".$leftDistance."</div>";

    $rightDistance = 0;
    $restDistance = 5;
    $attempt = 0;
    while($restDistance != 0 && $attempt < 20){
        $currentDistance = CallAPI("PUT", $moveUrl, [
            'direction' =>  "right",
            'distance' => 5
        ]);

        if(!empty($currentDistance)){
            echo "<br> - RIGHT DISTANCE: " . $currentDistance;
            $temp = json_decode($currentDistance);
            $restDistance = $temp->distance;
            $rightDistance = $rightDistance + $restDistance;
        }else{
            $attempt = $attempt + 1;
        }
    }
    echo "<div>RIGHT TOTAL: ".$rightDistance."</div>";
    //RIGHT/2 should be WIDTH of the hall.
    $hallWidth = $rightDistance/2;
    //
    $upDistance = 0;
    $restDistance = 5;
    $attempt = 0;
    while($restDistance != 0 && $attempt < 20){
        $currentDistance = CallAPI("PUT", $moveUrl, [
            'direction' =>  "up",
            'distance' => 5
        ]);

        if(!empty($currentDistance)){
            echo "<br> - UP DISTANCE: " . $currentDistance;
            $temp = json_decode($currentDistance);
            $restDistance = $temp->distance;
            $upDistance = $upDistance + $restDistance;
        }else{
            $attempt = $attempt + 1;
        }
    }
    echo "<div>UP TOTAL: ".$upDistance."</div>";

    $downDistance = 0;
    $restDistance = 5;
    $attempt = 0;
    while($restDistance != 0 && $attempt < 20){
        $currentDistance = CallAPI("PUT", $moveUrl, [
            'direction' =>  "down",
            'distance' => 5
        ]);

        if(!empty($currentDistance)){
            echo "<br> - DOWN DISTANCE: " . $currentDistance;
            $temp = json_decode($currentDistance);
            $restDistance = $temp->distance;
            $downDistance = $downDistance + $restDistance;
        }else{
            $attempt = $attempt + 1;
        }
    }
    echo "<div>DOWN TOTAL: ".$downDistance."</div>";
    //DOWN/2 should be HEIGHT of the hall.
    $hallHeight = $downDistance/2;
    //

    echo "<div>HEIGHT: ".$hallHeight." | WIDTH: ".$hallWidth."</div>";

    echo "<br>" . round($hallHeight/5) ." - ". $hallHeight%5;
    echo "<br>" . round($hallWidth/5) ." - ". $hallWidth%5;

    $moveLeft = round($hallWidth/5);
    $restLeft = $hallWidth%5;
    $attempt = 0;

    echo "<br>MOVING TO THE MIDDLE - LEFT: " . $moveLeft;

    while($moveLeft > 0 && $attempt < 20){
        $currentDistance = CallAPI("PUT", $moveUrl, [
            'direction' =>  "left",
            'distance' => 5
        ]);

        if(!empty($currentDistance)){
            echo "<br> - LEFT DISTANCE: " . $currentDistance;

            $moveLeft--;
        }else{
            $attempt = $attempt + 1;
        }
    }
    $attempt = 0;
    while($restLeft > 0 && $attempt < 20){
        $currentDistance = CallAPI("PUT", $moveUrl, [
            'direction' =>  "left",
            'distance' => $restLeft
        ]);

        if(!empty($currentDistance)){
            echo "<br> - (REST) LEFT DISTANCE: " . $currentDistance;
            $restLeft = 0;
        }else{
            $attempt = $attempt + 1;
        }
    }
    echo "<div>LEFT DONE</div>";


    $moveUp = round($hallHeight/5);
    $restUp = $hallHeight%5;
    $attempt = 0;

    echo "<br>MOVING TO THE MIDDLE - UP: " . $moveUp;

    while($moveUp > 0 && $attempt < 20){
        $currentDistance = CallAPI("PUT", $moveUrl, [
            'direction' =>  "up",
            'distance' => 5
        ]);

        if(!empty($currentDistance)){
            echo "<br> - UP DISTANCE: " . $currentDistance;

            $moveUp--;
        }else{
            $attempt = $attempt + 1;
        }
    }
    $attempt = 0;
    while($restUp > 0 && $attempt < 20){
        $currentDistance = CallAPI("PUT", $moveUrl, [
            'direction' =>  "up",
            'distance' => $restUp
        ]);

        if(!empty($currentDistance)){
            echo "<br> - (REST) UP DISTANCE: " . $currentDistance;
            $restUp = 0;
        }else{
            $attempt = $attempt + 1;
        }
    }
    echo "<div>UP DONE</div>";
    $hasEscaped = CallAPI("PUT", "https://area51.serverzone.dev/robot/".$robotId."/escape", [
        'salary' =>  15000
    ]);
}

?>

<html>
    <head>

    </head>

    <body>
        <div>
            <?php

            echo "ID Robota: " . (empty($data) ? "ROBOT ZTRATIL ANTENKU... NEBO TY (REFRESHNI APPKU)" : $data->id);
            echo "<br>";
            echo "Has escaped: " . $hasEscaped;

            ?>
        </div>
    </body>
</html>