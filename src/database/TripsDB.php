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

class TripsDB {
    public function getTrips ($trip) {
        GLOBAL $database;
        return $database->select("Trips", [
            "ID",
            "From",
            'To',
            'Bus',
            'Driver',
            'Passengers',
            'Date',
            'Time',
        ],
        [
            "From"  => $trip->getFrom(),
            "To"    => $trip->getTo(),
            "Date"  => $trip->getDate(),
        ]);
    }

    public function getDriver ($ID) {
        GLOBAL $database;
        return $database->get('Drivers', [
            'Name',
            'BirthDate',
            'LicenseDate',
            'CompanyDate',
            'Salary',
        ], [
            'ID' => $ID,
        ]);
    }

    public function getBus ($ID) {
        GLOBAL $database;
        return $database->get('Drivers', [
            "Name",
            "EngineName",
            "EngineCapacity",
            "EngineHorsePower",
            "Mileage",
            "NextMaintenance",
            "TankCapacity",
            "FuelConsumption",
            "MaxPassengers",
            "Number",
        ], [
            'ID' => $ID,
        ]);
    }
}

?>