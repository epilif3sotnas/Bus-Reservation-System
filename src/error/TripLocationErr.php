<?php

class TripLocationErr {
    private $location;
    private $error;

    // error messages
    private const STRING_ERROR = '\nData inserted is not a string.\n';
    private const ALPHABETIC_ERROR = '\nData inserted should only have alphabetic characters.\n';

    public function __construct ($location, $error) {
        $this->location = $location;

        switch ($error) {
            case 'string':
                $this->error = SELF::STRING_ERROR;
                break;
            
            case 'alphabetic':
                $this->error = SELF::ALPHABETIC_ERROR;
                break;

            default:
                $this->error = $error;
        }
    }

    public function getLocation () {
        return $this->location;
    }

    public function getError () {
        return $this->error;
    }
}

?>