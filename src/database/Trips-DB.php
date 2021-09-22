<?php

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
        $trips = $database->select("Trips", [
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

        if (!$trips) {
            echo "\nOccurred an error 😞\n";
            return (object) ['trips' => null, 'isGetTrips' => false];
        }
        if (!$database->error) {
            return (object) ['trips' => $trips, 'isGetTrips' => true];
        }
        echo "\nOccurred an error 😞\n";
        return (object) ['trips' => null, 'isGetTrips' => false];
    }

    public function getDriver ($ID) {
        GLOBAL $database;
        $driver = $database->get('Drivers', [
            'Name',
            'BirthDate',
            'LicenseDate',
            'CompanyDate',
            'Salary',
        ], [
            'ID' => $ID,
        ]);

        if (!$driver) {
            return (object) ['driver' => null, 'isGetDriver' => false];
        }
        if (!$database->error) {
            return (object) ['driver' => $driver, 'isGetDriver' => true];
        }
        echo "\nOccurred an error 😞\n";
        return (object) ['driver' => null, 'isGetDriver' => false];
    }

    public function getBus ($ID) {
        GLOBAL $database;
        $bus = $database->get('Buses', [
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

        if (!$bus) {
            return (object) ['bus' => null, 'isGetBus' => false];
        }
        if (!$database->error) {
            return (object) ['bus' => $bus, 'isGetBus' => true];
        }
        echo "\nOccurred an error 😞\n";
        return (object) ['bus' => null, 'isGetBus' => false];
    }

    public function addPassenger ($ID) {
        global $database;
        $database->update('Trips',[
            'Passengers[+]' => 1,
        ], [
            'ID' => $ID,
        ]);

        return $database->error ? false : true;
    }

    public function subtractPassenger ($ID) {
        global $database;
        $database->update('Trips',[
            'Passengers[-]' => 1,
        ], [
            'ID' => $ID,
        ]);

        return $database->error ? false : true;
    }
}

?>