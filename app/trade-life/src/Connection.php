<?php
namespace TradeLife;
use PDO;
class Connection
{
    private $conn;
    private $status;
    //private $yodlee;
    public function __construct()
    {
        try {
        $this->conn = new PDO("pgsql:host=localhost;port=5432;dbname=tradelife;user=techcliks;password=tech123");
        if ($this->conn)
          return true;
          //$this->yodlee = new \YodleeApi\Client(env('YODLEEAPI_URL'));
          //$response = $yodleeApi->cobrand()->login(env('YODLEEAPI_COBRAND_LOGIN'), env('YODLEEAPI_COBRAND_PASSWORD'));
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
