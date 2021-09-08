<?php

class ClearCLI {
    // variables waiting time
    private const THREE_SECONDS = 3;
    private const FIVE_SECONDS = 5;

    public function __construct () {}

    public function clearZeroWaiting () {
        system('clear');
    }

    public function clearThreeWaiting () {
        sleep(SELF::THREE_SECONDS);
        system('clear');
    }

    public function clearFiveWaiting () {
        sleep(SELF::FIVE_SECONDS);
        system('clear');
    }

    public function sleepThree () {
        sleep(SELF::THREE_SECONDS);
    }

    public function sleepFive () {
        sleep(SELF::FIVE_SECONDS);
    }
}

?>