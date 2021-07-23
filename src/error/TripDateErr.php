<?php

class TripDateErr {
    private $date;
    private $error;

    // error messages
    private const TIME_ERROR = '\nDate inserted not supported.\n';

    public function __construct ($date, $error) {
        $this->date = $date;
        
        switch ($error) {
            case 'no time':
                $this->error = SELF::TIME_ERROR;
                break;

            default:
                $this->error = $error;
        }
    }

    public function getDate () {
        return $this->date;
    }

    public function getError () {
        return $this->error;
    }
}

?>