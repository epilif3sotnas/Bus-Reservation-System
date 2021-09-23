<?php

class CurrentBookingsDB {
    public function makeBook ($trip, $username) {
        global $tripsDB;
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
        $returnedCurrentBookings = $this->getBookingByUser($username);
        if (!$returnedCurrentBookings->isGetCurrentBookings) {
            return false;
        }
        global $database;
        $pdoObj = $database->delete('CurrentBookings', [
            'Passenger' => $username,
        ]);
        if ($pdoObj->rowCount() > 0) {
            global $tripsDB;
            foreach ($returnedCurrentBookings->currentBookings as $booking) {
                $tripsDB->subtractPassenger($booking['Trip']);
            }
        }

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
            global $tripsDB;
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