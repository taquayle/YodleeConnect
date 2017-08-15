<?php
namespace TradeLife;

use TradeLife\Api\User;
use TradeLife\Api\Transaction;
use TradeLife\Api\Profile;
use TradeLife\Api\Data;
use TradeLife\Api\UserProfile;
use TradeLife\Api\UserStocks;
class Client
{
    private $connection;
    private $session;

    public function __construct()
    {
        $this->session = new Session();
        return $this->connection = new Connection();

    }

    public function user()
    {
        return new User($this->connection, $this->session);
    }

    public function transaction()
    {
      return new Transaction($this->connection, $this->session);
    }

    public function profile()
    {
      return new Profile($this->connection, $this->session);
    }

    public function userstocks()
    {
      return new UserStocks($this->connection, $this->session);
    }

    public function userprofile()
    {
      return new UserProfile($this->connection, $this->session);
    }
    public function buildProfile()
    {

        $this->userprofile()->create();

        $this->userstocks()->generate();
        
        $this->profile()->update();

    }
    public function data()
    {
      return new Data($this->connection, $this->session);
    }
}

?>
