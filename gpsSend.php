<?php
include_once('corsHeaders.php');
include_once('dbAccess.php');

$lat = $_POST['lat'];
$lon = $_POST['lon'];
$userId = $_POST['userId'];

try{
    $pdo = new PDO('mysql:host='.$host.';dbname='.$database.';port='.$port, $usernameDB, $passwordDB);

    $data = [
        'lat' => $lat,
        'lon' => $lon,
        'userId' => $userId,
    ];
    $sql = "INSERT INTO gps_coordinates VALUES (NULL, :userId, :lat, :lon)";
    $stmt= $pdo->prepare($sql);
    $stmt->execute($data);

    echo json_encode("Gps coord sended!");

}catch(PDOException $e){
    echo 'Cant resolve db connection: ' . $e->getMessage();
}
?>