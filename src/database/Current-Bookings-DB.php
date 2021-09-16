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
        $database->insert('CurrentBookings', [
            'Trip'  => $Trip,
            'Passenger' => $username,
            'DateTimeBooking' => date('c'),
        ]);

        if (!$database->error) {
            echo "\nBook made successfully 😎\n";
            return true;
        }
        echo "\nError ocurred 😞, book wasn't made\n";
        return false;
    }

    public function getBookingByUser ($username) {
        GLOBAL $database;
        $bookings = $database->select('CurrentBookings', [
            'ID',
            'Trip',
            'Passenger',
            'DateTimeBooking',
        ], [
            'Passenger' => $username,
        ]);

        if (!$bookings) {
            echo "\nOccurred an error 😞\n";
            return (object) ['currentBookings' => null, 'isGetCurrentBookings' => false];
        }
        if (!$database->error) {
            return (object) ['currentBookings' => $bookings, 'isGetCurrentBookings' => true];
        }
        echo "\nOccurred an error 😞\n";
        return (object) ['currentBookings' => null, 'isGetCurrentBookings' => false];
    }
}

?>