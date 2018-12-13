<?php
include_once('corsHeaders.php');
include_once('dbAccess.php');

$userId = $_GET['userId'];

try{
    $pdo = new PDO('mysql:host='.$host.';dbname='.$database.';port='.$port, $usernameDB, $passwordDB);

    /*$archives = $pdo->prepare("SELECT id, `from`, `to`, GROUP_CONCAT(document_name SEPARATOR ', ') FROM loads
LEFT JOIN related_documents ON loads.id = load_id
WHERE user_id = :userId AND status = 6
GROUP BY load_id");
    */
    $archives = $pdo->prepare('
        SELECT route_id AS id, CONCAT(loading.post_code, " ", loading.place) AS `from`, CONCAT(unloading.post_code, " ", unloading.place) AS `to`, phone, GROUP_CONCAT(document_name SEPARATOR ", ") AS document_name FROM `route`
        JOIN (SELECT route_address_id, place, post_code FROM route_address) AS loading ON loading.route_address_id = route.loading_address_id
        JOIN (SELECT route_address_id, place, post_code FROM route_address) AS unloading ON unloading.route_address_id = route.unloading_address_id
        JOIN (SELECT id, phone FROM fos_user) AS fuser ON fuser.id = route.user_id
        LEFT JOIN related_documents ON route_id = related_documents.load_id
        WHERE route_status_id = 5 AND driver_id = :userId
        GROUP BY related_documents.load_id
        ORDER BY id
    ');
    $archives->bindParam(':userId', $userId, PDO::PARAM_STR, 12);
    $archives->execute();
    $archives = $archives->fetchAll();

    if($archives){
        $response = json_encode($archives);
    }
    else $response = array('archives' => 0);

    echo json_encode($response);

}catch(PDOException $e){
    echo 'Cant resolve db connection: ' . $e->getMessage();
}

?>