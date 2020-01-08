<?php
header("Content-Type: application/json");
require "../core/global.inc.php";
$global = new GlobalHandler(false);

$database = $global->db;
$functions = $global->functions;
if(isset($_POST['sessionString']) && isset($_POST['userID'])) {
    // Validate Session String to verify it's legit
    if($functions->checkSessionString($_POST['userID'], $_POST['sessionString']) === false) {
        http_response_code(403);
    } else {
        if (isset($_POST["limit"], $_POST["start"])) {
            if (isset($_POST['profileID'])) {
                if ($_POST['profileID'] == $_POST['userID']) {
                    $queryRaw = "SELECT users_posts.*, users.FirstName, users.LastName FROM users_posts INNER JOIN users ON users_posts.UserID = users.UserID WHERE users_posts.UserID={$_POST['userID']} OR users_posts.ProfileID={$_POST['userID']} ORDER BY PostID DESC LIMIT " . $_POST["start"] . ", " . $_POST["limit"] . "";
                } else {
                    $queryRaw = "SELECT DISTINCT users_posts.*, users.FirstName, users.LastName FROM users_posts INNER JOIN users ON users_posts.UserID = users.UserID LEFT JOIN users_friends ON users_friends.UserID = users.UserID WHERE (users_posts.ProfileID={$_POST['profileID']}) OR (users_friends.FriendID={$_POST['userID']} AND users_friends.UserID={$_POST['profileID']}) ORDER BY PostID DESC LIMIT " . $_POST["start"] . ", " . $_POST["limit"] . "";
                }
            } else {
                $queryRaw = "SELECT DISTINCT users_posts.*, users.FirstName, users.LastName FROM users_posts INNER JOIN users ON users_posts.UserID = users.UserID LEFT JOIN users_friends ON users_friends.UserID = users.UserID WHERE users_friends.FriendID={$_POST['userID']} OR users_posts.UserID={$_POST['userID']} ORDER BY PostID DESC LIMIT " . $_POST["start"] . ", " . $_POST["limit"] . "";
            }
            $query = $database->executeQuery($queryRaw);
            $posts = array();
            $postIndex = 0;
            while ($rows = $query->fetch_array()) {
                $postData = array(
                    "PostID" => $rows['PostID'],
                    "UserID" => $rows['UserID'],
                    "Content" => $rows['Content'],
                    "Date" => $rows['Date'],
                    "ProfileID" => $rows['ProfileID'],
                    "FirstName" => $rows['FirstName'],
                    "LastName" => $rows['LastName']
                );
                $posts[$postIndex] = $postData;
                $postIndex++;
            }
            echo json_encode($posts, JSON_UNESCAPED_SLASHES);
        } else {
            http_response_code(403);
        }
    }
} else {
    http_response_code(403);
}