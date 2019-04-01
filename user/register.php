<?php
header("Content-Type: application/json");
require "../core/global.inc.php";
$global = new GlobalHandler();
$functions = $global->functions;
$database = $global->db;

$array = null;
$error = false;

if(isset($_POST['strEmail']) && isset($_POST['strPassword'])) {
    $firstName = $_POST['strFirstName'];
    $lastName = $_POST['strLastName'];
    $email = $_POST['strEmail'];
    $password = $_POST['strPassword'];
    $confirmPassword = $_POST['strConfirmPassword'];
    $dob = $_POST['dob'];
    $gender = isset($_POST['strGender']) ? $_POST['strGender'] : "M";
    $error = false;
    $endResult = "";
    $array = null;

    if(Configuration::registerDisabled) {
        $endResult .= "Update Registration has been disabled.";
        $error=true;
    } else {
        if($firstName == "") {
            $endResult .= "Please enter your First Name.\n";
            $error=true;
        }
        if($lastName == "") {
            $endResult .= "Please enter your Last Name.\n";
            $error=true;
        }
        if($email == "") {
            $endResult .= "Please enter your Email Address.\n";
            $error=true;
        }
        if($password == "") {
            $endResult .= "Please enter a Password.\n";
            $error=true;
        }
        if($confirmPassword == "") {
            $endResult .= "Please confirm your Password.\n";
            $error=true;
        }
        if($dob == "") {
            $endResult .= "Please enter your Date of Birth.\n";
            $error=true;
        }
        if($gender == "") {
            $endResult .= "Please select a Gender.\n";
            $error=true;
        }
        if($password != $confirmPassword) {
            $endResult .= "The inputted passwords don't match.\n";
            $error=true;
        }
    }

    if(!$error)
    {
        if($database->getNumberOfRows("SELECT UserID FROM users WHERE Email LIKE '{$email}'") > 0) {
            $error = true;
            $endResult = "The email that you entered is already in use. Please try again";
        } else {
            $password = $functions->encryptPassword($email, $password);
            $database->executeQuery("INSERT INTO users (`FirstName`, `LastName`, `Password`, `Email`, `DoB`, `Gender`) VALUES ('{$firstName}', '{$lastName}', '{$password}', '{$email}', '{$dob}', '{$gender}');") or $error=true;
            if($error) {
                $endResult = "There was an error when creating your account. Please try again later.";
            }
            else
            {
                $endResult = "Welcome to Update.";
            }
        }
    }
} else {
    $error = true;
    $endResult = "There was an error whilst processing your request.";
}

$array = array(
    "status" => !$error,
    "message" => $endResult
);

echo json_encode(
    $array,
    JSON_UNESCAPED_SLASHES
);

?>