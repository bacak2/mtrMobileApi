<?php
include_once('corsHeaders.php');
include_once('dbAccess.php');
include_once('sms.php');

$newStatus = $_POST['newStatus'];
$takenLoadId = $_POST['takenLoadId'];

try{
    $pdo = new PDO('mysql:host='.$host.';dbname='.$database.';port='.$port, $usernameDB, $passwordDB);
    
    if($newStatus == 3 || $newStatus == 4){
        $smsSended = sendSms($takenLoadId, $newStatus, $pdo);
    }
    elseif($newStatus == 5){
        $data = array(
            'newStatus' => $newStatus,
            'takenLoadId' => $takenLoadId,
        );
        $sql = "UPDATE route SET route_status_id = :newStatus WHERE route_id = :takenLoadId";
        $stmt= $pdo->prepare($sql);
        $stmExecuted = $stmt->execute($data);
    }
    
    if($stmExecuted || $smsSended) echo json_encode("true");
    else echo json_encode("false");

}catch(PDOException $e){
    echo 'Cant resolve db connection: ' . $e->getMessage();
}
?>