<?php
include_once('corsHeaders.php');
include_once('dbAccess.php');

try{
    $pdo = new PDO('mysql:host='.$host.';dbname='.$database.';port='.$port, $usernameDB, $passwordDB);

    $data = [
        'userId' => $_POST['userId'],
        'weight' => $_POST['weight'],
        'usedSpace' => $_POST['usedSpace'],
    ];
    $sql = "INSERT INTO load_info VALUES (NULL, :userId, :weight, :usedSpace)";
    $stmt= $pdo->prepare($sql);
    if($stmt->execute($data)) echo json_encode("true");
    else echo json_encode("false");

}catch(PDOException $e){
    echo 'Cant resolve db connection: ' . $e->getMessage();
}
?>