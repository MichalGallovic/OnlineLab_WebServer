<?php

/**
 * Handling database connection
 */
class DbConnect {

    private $conn;

    function __construct() {        
    }

    /**
     * Establishing database connection
     * @return database connection handler
     */
    function connect() {
        include_once dirname(__FILE__) . '/config.php';

        // Connecting to mysql database
        $this->conn = new mysqli(DB_SERVER, DB_SERVER_USERNAME, DB_SERVER_PASSWORD, DB_DATABASE);

        // Check for database connection error
        if (mysqli_connect_errno()) {
            echo "Failed to connect to MySQL: " . mysqli_connect_error();
        }

        // Change character set to utf8
        if (!$this->conn->set_charset(DB_ENCODING)) {
            echo "Error loading character set utf8: " . $this->conn->error;
        }

        // returing connection resource
        return $this->conn;
    }

}

?>
