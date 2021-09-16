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

class PastBookingsDB {
    public function getBookingByUser ($username) {
        GLOBAL $database;
        $bookings = $database->select('PastBookings', [
            'ID',
            'Trip',
            'Passenger',
            'DateTimeBooking',
            'DateTimeClosed',
        ], [
            'Passenger' => $username,
        ]);

        if (!$bookings) {
            echo "\nOccurred an error 😞\n";
            return (object) ['pastBookings' => null, 'isGetPastBookings' => false];
        }
        if (!$database->error) {
            return (object) ['pastBookings' => $bookings, 'isGetPastBookings' => true];
        }
        echo "\nOccurred an error 😞\n";
        return (object) ['pastBookings' => null, 'isGetPastBookings' => false];
    }
}

?>