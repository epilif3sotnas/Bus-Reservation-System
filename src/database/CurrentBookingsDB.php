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

class CurrentBookingsDB {

    public function makeBook ($Trip, $username) {
        GLOBAL $database;
        return $database->insert('CurrentBookings', [
            'Trip'  => $Trip,
            'Passenger' => $username,
        ]);
    }

    public function getBookingByUser ($username) {
        GLOBAL $database;
        return $database->select('CurrentBooking', [
            'ID',
            'Trip',
            'Passenger',
            'DateTimeBooking',
        ], [
            'Passenger' => $username,
        ]);
    }
}

?>