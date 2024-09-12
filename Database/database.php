<?php
    require_once "config.php";

    class Database {
        public static $instance;
        public $connection;
        private $db;

        public function __construct() {
            if(!isset(self::$instance)) {
                self::$instance = $this;
            }

            $this->openConnection();
        }

        public function openConnection() {
            self::$instance->connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            if (self::$instance->connection->connect_error) {
                die("Database connection failed: " . self::$instance->connection->connect_error);
            }

            return self::$instance->connection;
        }

        public function makeQuery($query) {
            $result = mysqli_query(self::$instance->connection, $query);
            return $result;
        }
    }
?>