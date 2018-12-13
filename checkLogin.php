<?php
include_once('corsHeaders.php');
include_once('dbAccess.php');

$login = $_GET['user'];
$password = $_GET['password'];
$password = sha1($password);

try{
    $pdo = new PDO('mysql:host='.$host.';dbname='.$database.';port='.$port, $usernameDB, $passwordDB);
    $user = $pdo->prepare('SELECT id, login, password FROM users WHERE login = :login AND password = :password');
    $user->bindParam(':login', $login, PDO::PARAM_STR, 12);
    $user->bindParam(':password', $password, PDO::PARAM_STR, 12);
    $user->execute();
    $user = $user->fetchObject();

    if($user){
        $response = array(
            'id' => $user->id,
            'login' => $user->login,
        );
    }
    else{
        $response = array(
            'id' => 0,
            'errorMessage' => 'Niepoprawny login lub haslo'
        );
    }
    echo json_encode($response);

}catch(PDOException $e){
    echo 'Cant resolve db connection: ' . $e->getMessage();
}
?>