<?php 

class User {
    private $username;
    private $password;

    public function __construct ($username, $password) {
        $this->username = $username;
        $this->password = $password;
    }

    public function getUsername () {
        return $this->username;
    }
    
    public function getPassword () {
        return $this->password;
    }

    public function generateHashPassword () {
        return password_hash($this->password, PASSWORD_BCRYPT);
    }
}

?>