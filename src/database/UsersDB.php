<?php

require '../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

$database = new Medoo\Medoo([
    'type'      => $_ENV['TYPE_DB'],
    'host'      => $_ENV['HOST_DB'],
    'database'  => $_ENV['DATABASE_DB'],
    'username'  => $_ENV['USERNAME_DB'],
    'password'  => $_ENV['PASSWORD_DB'],

    'error'     => PDO::ERRMODE_WARNING,
]);

class UsersDB {
    
    public function insertUser ($username, $password) {
        GLOBAL $database;
        $database->insert("Users", [
            "Username" => $username,
            "Password" => $password,
        ]);
        return $database->error;    // return errors messages
    }

    public function authenticationUser ($username, $password) {
        GLOBAL $database;
        $passwordFromDB = $database->get("Users", "Password", [
            "Username" => $username,
        ]);
        return password_verify($password, $passwordFromDB);
    }

    public function getInformationUser ($username) {
        GLOBAL $database;
        return $database->get("Users", [
            "DateAccountCreation",
            "DatePasswordModification",
        ],
        [
            "Username" => $username,
        ]);
    }
}

?>