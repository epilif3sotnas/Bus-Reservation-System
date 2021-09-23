<?php

class ClearCLI {
    // variables waiting time
    private const ONE_SECOND = 1;
    private const THREE_SECONDS = 3;

    public function clearZeroWaiting () {
        system('clear');
    }

    public function clearThreeWaiting () {
        sleep(SELF::THREE_SECONDS);
        system('clear');
    }

    public function sleepOne () {
        sleep(SELF::ONE_SECOND);
    }

    public function sleepThree () {
        sleep(SELF::THREE_SECONDS);
    }
}

?>