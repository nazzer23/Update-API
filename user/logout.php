<?php
header("Content-Type: application/json");
require "../core/global.inc.php";
$global = new GlobalHandler(false);

$template = $global->template;
$database = $global->db;
$functions = $global->functions;

if(isset($_POST['sessionString']) && isset($_POST['userID'])) {
    if($functions->getUserDataFromSession($_POST['userID'],$_POST['sessionString']) == false) {
		http_response_code(403);
    } else {
		$userData = $functions->getUserDataFromSession($_POST['sessionString']);
		
		// Set Session String to null and log user out
		// Set SessionID to active nopeeee
		$database->executeQuery("UPDATE users_sessions SET Valid=0 WHERE SessionString='{$_POST['sessionString']}'");
		
        echo json_encode(
            array(
                "success" => true
                )
            , JSON_UNESCAPED_SLASHES);
    }
} else {
    http_response_code(403);
}
?>