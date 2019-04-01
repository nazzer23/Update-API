<?php
class Functions {
    public $global;
    public $database;

    public function __construct($global) {
        $this->global = $global;
        $this->database = $global->db;
    }

    public function encryptPassword($x, $y) {
        $user = strtolower($x);
        $pass = $y;

        $data = $pass . $user;
        $data = hash("sha512", $data);
        $data = md5($data);
        $data = hash("sha512", base64_encode($data));
        $data = strrev($data);
        $data = strtoupper($data);
        $data = substr($data, strlen($user), 26);
        $data = strrev($data);

        return $data;
    }

    public function generateSessionString($length, $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ') {
        $pieces = [];
        $max = mb_strlen($keyspace, '8bit') - 1;
        for ($i = 0; $i < $length; ++$i) {
            $pieces []= $keyspace[random_int(0, $max)];
        }
        return implode('', $pieces);
    }

    public function getUserDataFromSession($sessionString) {
        $userQuery = $this->database->executeQuery("SELECT * FROM users INNER JOIN users_sessions ON users.UserID = users_sessions.UserID WHERE users_sessions.SessionString='{$sessionString}' AND users_sessions.Valid=1");
        if($userQuery->num_rows <= 0) {
            return false;
        } else {
            return $userQuery->fetch_object();
        }
    }

    public function getDateFormat($date) {
        $databaseTime = strtotime($date);
        return date("l jS F Y g:ia", $databaseTime);
    }

    public function cors() {

        // Allow from any origin
        if (isset($_SERVER['HTTP_ORIGIN'])) {
            // Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
            // you want to allow, and if so:
            header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Max-Age: 86400');    // cache for 1 day
        }
    
        // Access-Control headers are received during OPTIONS requests
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
                // may also be using PUT, PATCH, HEAD etc
                header("Access-Control-Allow-Methods: GET, POST, OPTIONS");         
    
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
                header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
    
        }
    }

}
?>