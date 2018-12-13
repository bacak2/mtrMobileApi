<?php
include_once('corsHeaders.php');
include_once('dbAccess.php');

try{
    $pdo = new PDO('mysql:host='.$host.';dbname='.$database.';port='.$port, $usernameDB, $passwordDB);

    //$notifications = $pdo->prepare('SELECT id, `from`, `to` FROM loads WHERE status = 1');

    $notifications = $pdo->prepare('
        SELECT truck_id AS id, CONCAT(loading.post_code, " ", loading.place) AS `from`, CONCAT(unloading.post_code, " ", unloading.place) AS `to`, phone  FROM `truck`
        JOIN (SELECT truck_address_id, place, post_code FROM truck_address) AS loading ON loading.truck_address_id = truck.loading_address_id
        JOIN (SELECT truck_address_id, place, post_code FROM truck_address) AS unloading ON unloading.truck_address_id = truck.unloading_address_id
        JOIN (SELECT id, phone FROM fos_user) AS fuser ON fuser.id = truck.user_id
        WHERE truck_status_id = 1
        ');
    $notifications->execute();
    $notifications = $notifications->fetchAll();

    if($notifications){
        $response = json_encode($notifications);
    }
    else $response = array('notifications' => 0);

    echo json_encode($response);

}catch(PDOException $e){
    echo 'Cant resolve db connection: ' . $e->getMessage();
}
?>