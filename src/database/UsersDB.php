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
        global $database;
        $database->insert('Users', [
            'Username' => $username,
            'Password' => $password,
            'DateAccountCreation' => date('c'),
            'DatePasswordModification' => date('c'),
        ]);

        if (!$database->error) {
            echo "\nAccount created successfully 😎\n";
            return true;
        }
        echo "\nError ocurred 😞, account wasn't created\n";
        echo $database->error;
        return false;
    }

    public function authenticationUser ($username, $password, $sessionSecurity = null) {
        if (isset($_SESSION['P'])) {
            if ($password == $sessionSecurity->decryptRSA($_SESSION['P'])) {
                return true;
            }
            echo "\nAuthentication failed\n";
            return false;
        }

        global $database;
        $passwordFromDB = $database->get("Users", "Password", [
            "Username" => $username,
        ]);

        if ($database->error) {
            echo "\nError ocurred 😞\n";
            echo $database->error;
            return false;
        }

        if (password_verify($password, $passwordFromDB)) {
            echo "\nLogged in...";
            return true;
        }
        echo "\nAuthentication failed\n";
        return false;
    }

    public function getInformationUser ($username) {
        global $database;
        $userInfo = $database->get("Users", [
            "DateAccountCreation",
            "DatePasswordModification",
        ],
        [
            "Username" => $username,
        ]);

        if (!$database->error) {
            return (object) ['info' => $userInfo, 'isGetInfo' => true];
        }
        echo "\nOccurred an error 😞\n";
        return (object) ['info' => null, 'isGetInfo' => false];
    }

    public function changePassword ($username, $password) {
        global $database;
        $database->update('Users', [
            'Password' => $password,
            'DatePasswordModification' => date('c'),
        ],
        [
            'Username' => $username
        ]);

        if (!$database->error) {
            echo "\nPassword changed successfully\n";
            return true;
        }
        echo "\nOccurred an error 😞\nPassword wasn't changed\n";
        echo $database->error;
        return false;
    }

    public function isAvailableUsername ($username) {
        global $database;
        $countUsers = $database->count('Users', [
            'Username' => $username,
        ]);

        if (!$database->error && $countUsers == 0) {
            return true;
        }
        if ($countUsers >= 1) {
            echo "\nUsername not available\n";
            return false;
        }
        echo "\nOccurred an error 😞\n";
        return false;
    }
}

?>