<?php

class PastBookingsDB {
    public function getBookingByUser ($username) {
        global $database;
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