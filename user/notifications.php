<?php
header("Content-Type: application/json");
require "../core/global.inc.php";
$global = new GlobalHandler(false);

$template = $global->template;
$database = $global->db;
$functions = $global->functions;

if(isset($_POST['sessionString'])) {
    if($functions->getUserDataFromSession($_POST['sessionString']) == false) {
		http_response_code(500);
    } else {
		$userData = $functions->getUserDataFromSession($_POST['sessionString']);
        echo json_encode(
            array(
                "notifCount" => $database->getNumberOfRows("SELECT * FROM users_notifications WHERE UserID='{$userData->UserID}' AND ReadNotif=0"),
                "msgCount" => "WIP",
                "friendCount" => $database->getNumberOfRows("SELECT * FROM users_friends_requests WHERE UserID='{$userData->UserID}'")
                )
            , JSON_UNESCAPED_SLASHES);
    }
} else {
    http_response_code(500);
}
?>