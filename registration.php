<?php
include_once('corsHeaders.php');
include_once('dbAccess.php');

$login = $_POST['user'];
$password = $_POST['password'];

try{

    $pdo = new PDO('mysql:host='.$host.';dbname='.$database.';port='.$port, $usernameDB, $passwordDB);

    $user = $pdo->prepare('SELECT id FROM users WHERE login = :login');
    $user->bindParam(':login', $login, PDO::PARAM_STR, 12);
    $user->execute();
    $user = $user->fetchObject();

    if($user){
        $response = array(
            'id' => 0,
            'message' => 'Użytkownik o podanym loginie już istnieje'
        );
    }
    else{
        $password = sha1($password);

        $data = [
            'login' => $login,
            'password' => $password,
        ];
        $sql = "INSERT INTO users VALUES (NULL, :login, :password)";
        $stmt= $pdo->prepare($sql);
        $stmt->execute($data);
        $id = $pdo->lastInsertId();
        $response = array(
            'id' => $id,
            'login' => $login,
            'message' => 'Utworzono nowego użytkownika'
        );
    }

    echo json_encode($response);

}catch(PDOException $e){
    echo 'Cant resolve db connection: ' . $e->getMessage();
}
?>