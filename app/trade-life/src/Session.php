<?php
namespace TradeLife;

class Session
{
    public $userName;

    public function __construct()
    {
      $this->userName = NULL;
    }

    public function setUser($userName)
    {
      $this->userName = $userName;
    }

    public function getUser()
    {
      return $this->userName;
    }

    public function test()
    {
      echo "session.php: Hello World";
    }
}

?>
