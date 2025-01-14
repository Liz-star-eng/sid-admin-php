<?php
class Operations
{

    private $dbhost = 'localhost';
    private $dbname = 'db_selfservice';
    private $dbuser = 'root';
    private $dbpassword = 'Elisab789';

    public function dbconnection()
    {
        try {

            $conn = new PDO("mysql:host=" . $this->dbhost . ";dbname=" . $this->dbname, $this->dbuser,  $this->dbpassword);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conn;
            
        } catch (PDOException $e) {
            echo "connection error" . $e->getMessage();
            exit;
        }
    }
}



// $database = new Operations();
// $conn = $database->dbconnection();

// if ($conn) {
//     echo "Connected to the database successfully!";
// } else {
//     echo "Failed to connect to the database.";
// }
