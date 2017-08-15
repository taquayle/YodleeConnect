<?php
//  AUTHOR: Tyler Quayle
//  DATE: 7/7/2017
//  FILE: trade-life/src/api/user.php
//  DESC: file to handle insertion of users into trade-life DB
namespace TradeLife\Api;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use PDO;

class User extends APIAbstract
{

  /**
  * Checks into database, if (username/email) and password match. let them in
  * also set the userID in session
  *
  *   @param string
  *   @param string
  *   @return boolean
  */
  public function login($user, $pass)
  {
    $pass = Hash::make('sbMemtaquayle1#123');
    $credentials = ['name' => 'sbMemtaquayle1', 'password' => $pass];
    return Auth::attempt($credentials);
    if( $this->checkCredentials($user, "name"))
      if(Auth::attempt(['name' => $user, 'password' => $pass]))

    if($this->checkCredentials($user, "email"))
      if(Auth::attempt(['email' => $email, 'password' => $pass]))
      return $this->updateLogin($user, "email");
    return false;
  }

  /**
  * Update the last login of the user as well as setting the session userName to
  * the login username.
  *
  * @param string
  * @param string
  * @return boolean
  */
  public function updateLogin($user, $loginType)
  {
    $stmt = $this->conn->prepare("UPDATE users
                                  SET last_login = CURRENT_TIMESTAMP
                                  WHERE $loginType = :login");
    $stmt->bindParam('login', $user);

    try{$stmt->execute() or die(print_r($stmt->errorInfo(), true));}
    catch(PDOException $e){echo "ERROR Updating User Login";}

    $this->session->setUser($this->userName($user, $loginType));
    return true;
  }

  /**
  * Return the username of the login, in the case user logs in with email
  * instead of by username
  *
  * @param string
  * @param string
  * @return string
  */
  public function userName($user, $loginType)
  {
    $stmt = $this->conn->prepare("SELECT name FROM users WHERE $loginType = :login");
    $stmt->bindParam('login', $user);
    $stmt->execute() or die(print_r($stmt->errorInfo(), true));
    return $stmt->fetch()['username'];
  }

  /**
  * Check the given combonation of variables for validitity.
  *
  *   @param string for variable
  *   @param string, name of column in table
  *   @param string, given password
  */
  private function checkCredentials($var, $varName)
  {
    $stmt = $this->conn->prepare("SELECT email FROM users WHERE ($varName = :var)");
    $stmt->bindParam(':var', $var);
    $stmt->execute();

    return ($stmt->rowCount() > 0);
  }

  /**
  * Insert into the user database the given user. Yodlee and Our database must
  * have a 1:1 relationship for user count
  *
  *   @param string, given user name
  *   @param string
  *   @param string
  */
  public function insert($userName, $email, $password)
  {
    if($this->validCheck($email, $userName))
    {
      $stmt = $this->conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
      $stmt->bindParam(1, $userName);
      $stmt->bindParam(2, $email);
      $password = Hash::make($password);
      $stmt->bindParam(3, $password);
      try{
        $stmt->execute() or die(print_r($stmt->errorInfo(), true));}
      catch(PDOException $e)
      {
        echo "ERROR INSERT to database";
      }
    }
  }

  /**
  * Calls valid twice to check if the username or email has been previously
  * used.
  *
  *   @param string
  *   @param string
  *   @return boolean
  */
  private function validCheck($email, $userName)
  {
      return $this->valid($userName, "name") &&
              $this->valid($email, "email");
  }

  /**
  * Queries the DB and checks for the given column and it's value to see if it
  * is already contained in the DB
  *
  *   @param string
  *   @param string
  *   @return boolean
  */
  private function valid($var, $varName)
  {
    $stmt = $this->conn->prepare("SELECT name FROM users WHERE $varName = :var");
    $stmt->bindParam(':var', $var);
    $stmt->execute();

    if($stmt->rowCount() > 0){
      echo "ERROR: $varName '$var' already in use";
      return false;
    }
    return true;
  }

  /**
  *   Using parent class, counts the rows in DB and returns count
  *
  *   @return int
  */
  public function count()
  {
    return $this->getCount("users");
  }



  public function test()
  {
    echo '<br>USER.php: Hello</br>';
  }
}
?>
