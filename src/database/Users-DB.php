<?php

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
            return false;
        }

        if (password_verify($password, $passwordFromDB)) {
            echo "\nLogged in...\n";
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

        if (!$userInfo) {
            echo "\nOccurred an error 😞\n";
            return (object) ['info' => null, 'isGetInfo' => false];
        }
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
            echo "\nUsername not available 😞\n";
            return false;
        }
        echo "\nOccurred an error 😞\n";
        return false;
    }

    public function deleteAccount ($username) {
        global $currentBookingsDB;
        if (!$currentBookingsDB->deleteAllCurrentBookingsByUser($username)) {
            echo "\nOccurred an error 😞\n";
            return false;
        }

        global $database;
        $database->delete('Users', [
            'Username' => $username,
        ]);
        if (!$database->error) {
            echo "\nAccount deleted successfully 😞\n";
            return true;
        }
        echo "\nOccurred an error 😞\nAccount deletion wasn't succesful\n";
        return false;
    }
}

?>