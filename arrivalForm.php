<?php
include_once('corsHeaders.php');
include_once('dbAccess.php');

try{
    $pdo = new PDO('mysql:host='.$host.';dbname='.$database.';port='.$port, $usernameDB, $passwordDB);

    $data = array(
        'newStatus' => $_POST['newStatus'],
        'takenLoadId' => $_POST['takenLoadId'],
    );
    $sql = "UPDATE loads SET status = :newStatus WHERE id = :takenLoadId";
    $stmt= $pdo->prepare($sql);
    if($stmt->execute($data)) echo json_encode("true");
    else echo json_encode("false");

}catch(PDOException $e){
    echo 'Cant resolve db connection: ' . $e->getMessage();
}
?>