<?php

class User {
    private $username;
    private $password;

    public function setUsername ($username) {
      if (isset($_SESSION['U'])) {
        $this->username = $username;
        return true;
      }
      
      $usersDB = new UsersDB();

      if ($usersDB->isAvailableUsername($username)) {
        $this->username = $username;
        return true;
      }
      $this->username = null;
      $this->password = null;
      return false;
    }

    public function setPassword ($password) {
      if ($this->isStrongPassword($password)) {
          $this->password = $password;
          echo "\nStrong Password 💪\n";
          return true;
      }
      echo "\nPassword doesn't match the requirements 😞\n";
      $this->username = null;
      $this->password = null;
      return false;
    }

    public function getUsername () {
        return $this->username;
    }
    
    public function getPassword () {
        return $this->password;
    }

    public function generateHashPassword () {
      try {
        return password_hash($this->password, PASSWORD_BCRYPT);
      } catch (Exception $e) {
        echo "\nOccurred an error 😞\n";
      }
    }

    private function isStrongPassword ($password) {
        if (strlen($password) < 8) {
          return false;
        }
      
        $countRequirements = 0;
      
        // check lowercase characters
        if (mb_strtoupper($password, "UTF-8") != $password) {
          $countRequirements++;
        }
      
        // check uppercase characters
        if (mb_strtolower($password, "UTF-8") != $password) {
          $countRequirements++;
        }
      
        // check numbers characters
        if (preg_match('~[0-9]+~', $password)) {
          $countRequirements++;
        }
      
        // check symbols characters
        if (!ctype_alnum($password)) {
          $countRequirements++;
        }
      
        return $countRequirements >= 3 ? true : false;
      }
}

?>