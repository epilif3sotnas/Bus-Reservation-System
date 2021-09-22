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

class CurrentBookingsDB {

    public function makeBook ($trip, $username) {
        $tripsDB = new TripsDB();
        $busReturned = $tripsDB->getBus($trip['Bus']);
        if (!$busReturned->isGetBus) {
            echo "\nError ocurred 😞, book wasn't made\n";
            return false;
        }
        if ($trip['Passengers'] >= $busReturned->bus['MaxPassengers']) {
            echo "\nBus is already full, you can't book this trip\n";
            return false;
        }

        global $database;
        $database->insert('CurrentBookings', [
            'Trip'  => $trip['ID'],
            'Passenger' => $username,
            'DateTimeBooking' => date('c'),
        ]);

        if (!$database->error) {
            if ($tripsDB->addPassenger($trip['ID'])) {
                echo "\nBook made successfully 😎\n";
                return true;
            }
        }
        echo "\nError ocurred 😞, book wasn't made\n";
        return false;
    }

    public function getBookingByUser ($username) {
        global $database;
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

    public function deleteAllCurrentBookingsByUser ($username) {
        global $database;
        $database->delete('CurrentBookings', [
            'Passenger' => $username,
        ]);
        return $database->error ? false : true;
    }

    public function deleteCurrentBookingByUser ($username, $tripID) {
        global $database;
        $database->delete('CurrentBookings', [
            'AND' => [
                'Passenger' => $username,
                'Trip' => $tripID,
            ]
        ]);

        if (!$database->error) {
            $tripsDB = new TripsDB();
            if ($tripsDB->subtractPassenger($tripID)) {
                echo "\nBook deleted successfully\n";
                return true;
            }
        }
        echo "\nOccurred an error 😞\n";
        return false;
    }
}

?>