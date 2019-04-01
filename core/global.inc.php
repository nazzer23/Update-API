<?php
require "config.php";
require "handlers/Database.php";
require "handlers/Functions.php";

class GlobalHandler {
    public $db;
    public $functions;

    public function __construct() {
        $this->db = new Database();
        $this->functions = new Functions($this);

        // Escape $_POST Strings
        if(isset($_POST)) { $_POST = $this->db->escapeArray($_POST); }
        if(isset($_GET)) { $_GET = $this->db->escapeArray($_GET); }

        $this->functions->cors();
    }

}
?>