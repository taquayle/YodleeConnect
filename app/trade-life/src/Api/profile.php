<?php

  namespace TradeLife\Api;
  use PDO;


  class Profile extends APIAbstract
  {

    public function update()
    {
      if($this->loggedIn())
      {
        $fileName =   $fileName = env('USER_PROFILE_REPO') . $this->session->getUser() . ".json";
        if(! file_exists($fileName) )
          echo "No user profile created. TradeLife/Api/Profile->create() first";
        else
        {
          $profile = json_decode(file_get_contents($fileName));

          if(! $this->checkDatabase($profile->UserName))
            $this->insertProfile($profile, "Basic");
          else {
            $this->updateProfile($profile, "Basic");
          }
        }
      }
      else {
        echo "No User Logged In";
      }
    }

    private function userArray($profile, $algo)
    {
      $insert_array = array();

      $keywords = get_object_vars($profile->Desc_Keywords);
      $stocks = get_object_vars($profile->Target_Companies->Default);
      $sectors = array_keys($stocks);

      $insert_array[] = $profile->UserName;
      // Get top 3 keywords
      for ($i=0; $i < 3; $i++)
        $insert_array[] = $keywords[$i]->Name;
      // Get Sector1, Stock1, Stock2, Stock3, Sector2, Stock1......
      for ($i=0; $i < 3; $i++) {
        $insert_array[] = $sectors[$i];
        for ($j=0; $j < 3; $j++) {
          $companies = get_object_vars($stocks[$sectors[$i]]);
          $insert_array[] = $companies[$j];
        }
      }
      $insert_array[] = $algo;
      return $insert_array;
    }

    private function updateProfile($profile, $algo)
    {
      $user_info_array = $this->userArray($profile, $algo);

      $stmt = $this->conn->prepare("UPDATE userprofile SET
                                  updated = CURRENT_TIMESTAMP, key1 = ?, key2 = ?, key3 = ?,
                                  sector1 = ?, sec1_comp1 = ?, sec1_comp2 = ?, sec1_comp3 = ?,
                                  sector2 = ?, sec2_comp1 = ?, sec2_comp2 = ?, sec2_comp3 = ?,
                                  sector3 = ?, sec3_comp1 = ?, sec3_comp2 = ?, sec3_comp3 = ?,
                                  algo = ?
                                  WHERE username = ?");
      $stmt->bindParam(17, $user_info_array[0]);
      for ($i=1; $i < count($user_info_array); $i++) {
        $stmt->bindParam($i, $user_info_array[$i]);
      }

      try{ $stmt->execute() or die(print_r($stmt->errorInfo(), true));}
      catch(PDOException $e){echo "ERROR UPDATE userprofile database";}
    }

    private function insertProfile($profile, $algo)
    {

      $user_info_array = $this->userArray($profile, $algo);
      $stmt = $this->conn->prepare("INSERT INTO userprofile
                                  (username, key1, key2, key3,
                                  sector1, sec1_comp1, sec1_comp2, sec1_comp3,
                                  sector2, sec2_comp1, sec2_comp2, sec2_comp3,
                                  sector3, sec3_comp1, sec3_comp2, sec3_comp3,
                                  algo)
                                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? )");
      for ($i=0; $i < count($insert_array); $i++) {
        $stmt->bindParam($i+1, $insert_array[$i]);
      }
      try{$stmt->execute() or die(print_r($stmt->errorInfo(), true));}
      catch(PDOException $e){echo "ERROR INSERT to userprofile database";}
    }

    private function checkDatabase($name)
    {
      $sql = "SELECT * FROM userprofile";
      $results = $this->conn->query($sql);

      if( count($results->fetchColumn()) > 0)
        return true;
      return false;
    }

    /**
    * Used to test if file was accessible.
    */
    public function test()
    {
      echo "PROFILE.php: Hello";
    }
  }
?>
