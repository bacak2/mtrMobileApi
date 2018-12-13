<?php
//Allow Headers
include_once('dbAccess.php');
header('Access-Control-Allow-Origin: *');

//print_r(json_encode($_FILES));
$file_name = urldecode($_FILES["file"]["name"]);
if($file_name == '') exit();
$new_image_name = substr($file_name, 0, strrevpos($file_name, ".")).".jpg";

//Move your files into upload folder
if(isset($_GET['loadId'])) $loadId = $_GET['loadId'];
else $loadId = 0;

if (!file_exists("uploadedFiles/$loadId")) {
    mkdir("uploadedFiles/$loadId", 0755, true);
}

move_uploaded_file($_FILES["file"]["tmp_name"], "uploadedFiles/$loadId/$new_image_name");

$data = [
    'load_id' => $loadId,
    'document_name' => $new_image_name,
];
$pdo = new PDO('mysql:host='.$host.';dbname='.$database.';port='.$port, $usernameDB, $passwordDB);
$sql = "INSERT INTO related_documents VALUES (:load_id, :document_name)";
$stmt= $pdo->prepare($sql);
$stmt->execute($data);

function strrevpos($instr, $needle){
    $rev_pos = strpos (strrev($instr), strrev($needle));
    if ($rev_pos===false) return false;
    else return strlen($instr) - $rev_pos - strlen($needle);
}

?>