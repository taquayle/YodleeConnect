<?php
// AUTH: Tyler Quayle
// DATE: 7/13/2017
// FILE: .php
// DESC:


namespace ExScrape;
use PDO;
class Connection
{
    private $conn;
    private $status;
    public function __construct()
    {
        try {
        $this->conn = new PDO("pgsql:host=localhost;port=5432;dbname=tradelife;user=techcliks;password=tech123");
        if ($this->conn)
            return true;
        }
        catch(PDOException $e)
        {
            return false;
        }
    }

    public function getConnection()
    {
        return $this->conn;
    }
}

?>
