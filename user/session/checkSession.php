<?php
header("Content-Type: application/json");

require "../../core/global.inc.php";
$global = new GlobalHandler();
$functions = $global->functions;
$database = $global->db;

$array = null;
$error = false;

if(isset($_POST['session']) && isset($_POST['userID'])) {
    $sessionString = $_POST['session'];
    $userID = $_POST['userID'];
    if($database->getNumberOfRows("SELECT * FROM users_sessions WHERE SessionString='{$sessionString}' AND UserID='{$userID}' AND Valid=1") > 0) {
        $array = array(
            "status" => true
        );
    } else {
        $array = array(
            "status" => false
        );
    }
} else {
    $error = true;
}

if($error) {
    $array = array(
        "status" => false,
        "message" => "There was an error whilst processing your request."
    );
}

echo json_encode(
    $array,
    JSON_UNESCAPED_SLASHES
);

?>