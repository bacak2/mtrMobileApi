<?php

use SMSApi\Client;
use SMSApi\Api\SmsFactory;
use SMSApi\Exception\SmsapiException;

require_once 'vendor/autoload.php';
header('Content-type: text/plain; charset=utf-8');

function sendSms($takenLoadId, $newStatus, $pdo){

    try{
        $takenLoad = $pdo->prepare('
            SELECT route_id AS id, CONCAT(loading.post_code, " ", loading.place) AS `from`, CONCAT(unloading.post_code, " ", unloading.place) AS `to`, stat.name AS status, phone  FROM `route`
            JOIN (SELECT route_address_id, place, post_code FROM route_address) AS loading ON loading.route_address_id = route.loading_address_id
            JOIN (SELECT route_address_id, place, post_code FROM route_address) AS unloading ON unloading.route_address_id = route.unloading_address_id
            JOIN (SELECT id, phone FROM fos_user) AS fuser ON fuser.id = route.user_id
            JOIN (SELECT route_status_id, name FROM route_status) AS stat ON stat.route_status_id = route.route_status_id
            WHERE route_id = :takenLoadId
        ');
    
        $takenLoad->bindParam(':takenLoadId', $takenLoadId, PDO::PARAM_STR, 12);
        $takenLoad->execute();
        $takenLoad = $takenLoad->fetchAll();
        $takenLoad = $takenLoad[0];
    }catch(PDOException $e){
        echo 'Cant resolve db connection: ' . $e->getMessage();
    }
    
    $client = new Client('wojciech.sendor@magda-trans.pl');
    $client->setPasswordHash('da725ab7a5a027b7fdcc6d35df14ba67');
    
    $smsapi = new SmsFactory;
    $smsapi->setClient($client);
    
    switch($newStatus){
        case 3: $newStatus = 'podjęty'; break;
        case 4: $newStatus = 'rozładowany'; break;
    }
    
    try {
        $actionSend = $smsapi->actionSend();
    
        $actionSend->setTo('792659089');
        $actionSend->setText("Trasa o ID {$takenLoad['id']} {$takenLoad['from']} > {$takenLoad['to']} zmieniła status na: $newStatus"); //{$takenLoad['status']}");
        $actionSend->setSender('mPanel'); //Pole nadawcy, lub typ wiadomości: 'ECO', '2Way'

//echo "Trasa o ID {$takenLoad['id']} {$takenLoad['from']} > {$takenLoad['to']} zmieniła status na: {$takenLoad['status']}";
//exit();
    
        $response = $actionSend->execute();
        
        return true;
        
    } catch (SmsapiException $exception) {
        echo 'ERROR: ' . $exception->getMessage();
    }
    
}