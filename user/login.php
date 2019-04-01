<?php
header("Content-Type: application/json");
require "../core/global.inc.php";
$global = new GlobalHandler();
$functions = $global->functions;
$database = $global->db;

$array = null;
$error = false;

if(isset($_POST['strEmail']) && isset($_POST['strPassword'])) {
    $email = $_POST['strEmail'];
    $password = $_POST['strPassword'];
    $hash = $functions->encryptPassword($email, $password);
    if($database->getNumberOfRows("SELECT * FROM users WHERE Email LIKE '{$email}' AND Password='{$hash}'") > 0) {
        $userData = $database->fetchObject("SELECT * FROM users WHERE Email LIKE '{$email}' AND Password='{$hash}'");
        if($userData->Access == 0) {
            $array = array(
                "status" => false,
                "message" => "You have been suspended from Update."
            );
        } else {
            $getSessionString = $functions->generateSessionString(64);
            $array = array(
                "status" => true,
                "sessionString" => $getSessionString,
                "userID" => $userData->UserID
            );
			
			$insertSession = $database->executeQuery("INSERT INTO users_sessions (UserID, SessionString) VALUES ('{$userData->UserID}', '{$getSessionString}')") or $error=1;
			if(!$insertSession) {
				$error = true;
			} else {
                $getLastIDQuery = $database->executeQuery("SELECT SessionID FROM users_sessions WHERE UserID='{$userData->UserID}' AND Valid=1 ORDER BY SessionID DESC");
                $sessionID = $getLastIDQuery->fetch_array()['SessionID'];
				$database->executeQuery("INSERT INTO users_login_activity (UserID, IP, SessionID, UserAgent) VALUES ('{$userData->UserID}', '{$_SERVER['REMOTE_ADDR']}', '{$sessionID}', '{$_SERVER['HTTP_USER_AGENT']}')") or $error=1;
			}
        }
    } else {
        $array = array(
            "status" => false,
            "message" => "The username and password combination you entered was incorrect."
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