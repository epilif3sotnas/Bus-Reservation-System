<?php 

include 'error/TripLocationErr.php';
include 'error/TripDateErr.php';

class Trip {
    private $from;
    private $to;
    private $date;

    public function setFrom ($from) {
        $fromStandard = $this->standardString($from);

        if (!$fromStandard->getError()) {
            $this->from = $from;
            return true;
        }

        echo $fromStandard->getError();
        $this->from = null;
        $this->to = null;
        $this->date = null;
        return false;
    }

    public function setTo ($to) {
        $toStandard = $this->standardString($to);

        if (!$toStandard->getError()) {
            $this->to = $to;
            return true;
        }
        
        echo $toStandard->getError();
        $this->from = null;
        $this->to = null;
        $this->date = null;
        return false;
    }

    public function setDate ($date) {
        $dateStandard = $this->dateToISO($date);

        if (!$dateStandard->getError()) {
            $this->date = $date;
            return true;
        }
        
        echo $dateStandard->getError();
        $this->from = null;
        $this->to = null;
        $this->date = null;
        return false;
    }

    public function getFrom () {
        return $this->from;
    }

    public function getTo () {
        return $this->to;
    }

    public function getDate () {
        return $this->date;
    }

    /* this function has the goal to standardize the input that are in the variables $from and $to
    the returned string will have all characters lowercase */

    public function standardString ($location) {
        try {
            if (gettype($location) != 'string') return new TripLocationErr('', 'string');

            foreach (explode(' ', $location) as $value) {
                if (!ctype_alpha($value)) return new TripLocationErr('', 'alphabetic');
            }

            return new TripLocationErr(strtolower($location), '');
        } catch (Exception $e) {
            return new TripLocationErr('', $e->getMessage());
        }
    }

    /* this function has the goal to standardize the input that are in the variable date
    the returned date will be in agreement with ISO 8601 year-month-day */

    public function dateToISO ($date) {
        try {
            $date = str_replace('/', '-', $date);
            $time = strtotime($date);
            if (!$time) return new TripDateErr('', 'no time');

            return new TripDateErr(DateTime::createFromFormat('d-m-Y', $date)->format('Y-m-d'), '');
        } catch (Exception $e) {
            return new TripDateErr('', $e->getMessage());
        }
    }
}

?>