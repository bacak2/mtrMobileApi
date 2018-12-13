<?php
include_once('corsHeaders.php');
include_once('dbAccess.php');

$userId = $_GET['userId'];

try{
    $pdo = new PDO('mysql:host='.$host.';dbname='.$database.';port='.$port, $usernameDB, $passwordDB);

    //$takenLoad = $pdo->prepare('SELECT id, status, `from`, `to` FROM loads WHERE user_id = :userId AND status IN (2,3,4,5)');
    $takenLoad = $pdo->prepare('
        SELECT route_id AS id, CONCAT(loading.post_code, " ", loading.place) AS `from`, CONCAT(unloading.post_code, " ", unloading.place) AS `to`, route_status_id AS status, phone  FROM `route`
        JOIN (SELECT route_address_id, place, post_code FROM route_address) AS loading ON loading.route_address_id = route.loading_address_id
        JOIN (SELECT route_address_id, place, post_code FROM route_address) AS unloading ON unloading.route_address_id = route.unloading_address_id
        JOIN (SELECT id, phone FROM fos_user) AS fuser ON fuser.id = route.user_id
        WHERE route_status_id IN (2,3,4) AND driver_id = :userId
    ');

    $takenLoad->bindParam(':userId', $userId, PDO::PARAM_STR, 12);
    $takenLoad->execute();
    $takenLoad = $takenLoad->fetchAll();

    if($takenLoad){
        $response = json_encode($takenLoad);
    }
    else $response = array('takenLoad' => 0);

    echo json_encode($response);

}catch(PDOException $e){
    echo 'Cant resolve db connection: ' . $e->getMessage();
}
?>