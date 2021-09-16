<?php

require '../vendor/autoload.php';

include 'class/User.php';
include 'class/Trip.php';
include 'class/Session-Security.php';

include 'database/Users-DB.php';
include 'database/Trips-DB.php';
include 'database/Current-Bookings-DB.php';
include 'database/Past-Bookings-DB.php';

include 'error/Trip-Location-Err.php';
include 'error/Trip-Date-Err.php';

include 'system/Clear-CLI.php';

$sessionSecurity = new SessionSecurity();

$usersDB = new UsersDB();
$tripsDB = new TripsDB();
$currentBookingsDB = new CurrentBookingsDB();
$pastBookingsDB = new PastBookingsDB();

$system = new ClearCLI();

$isTrue = true;
while ($isTrue) {
  $system->clearZeroWaiting();

  echo "\n\n------------------------Options------------------------";
  echo "\n1 - Create account";
  echo "\n2 - Login";
  echo "\nex - Exit\n";

  $option = readline();
  switch ($option) {
    case 'ex':   // exit
      $isTrue = false;
      echo "\nWe hope that you enjoy! 😎\n";
      $system->clearThreeWaiting();
      break;

    case '1':   // create account
      $isTrueCreateAccount = true;
      while ($isTrueCreateAccount) {
        $system->clearZeroWaiting();

        echo "\n\n------------------------Options------------------------";
        echo "\n1 - Create account";
        echo "\n0 - Return\n";
  
        $optionCreateAccount = readline();
        switch ($optionCreateAccount) {
          case '0':   // create account -> return
            $isTrueCreateAccount = false;
            break;
  
          case '1':   // create account -> create account
            $system->clearZeroWaiting();

            $user = new User();

            echo "\nCreation of an account\n";
            $username = readline('Insert your username: ');
            if (!$user->setUsername($username)) {
              $system->sleepThree();
              break;
            }

            echo "\nRequirements of password: ";
            echo "\n- Minimum 8 characters in length";
            echo "\n- Contains 3 of 4 of the following items:";
            echo "\n  - Uppercase Letters";
            echo "\n  - Lowercase Letters";
            echo "\n  - Numbers";
            echo "\n  - Symbols\n\n";

            echo 'Insert your password: ';
            $password = Seld\CliPrompt\CliPrompt::hiddenPrompt();

            if (!$user->setPassword($password)) {
              $system->sleepThree();
              break;
            }

            echo 'Confirm your password: ';
            $passwordConfirmation = Seld\CliPrompt\CliPrompt::hiddenPrompt();
            
            if ($password == $passwordConfirmation) {
              try {
                if (!$usersDB->insertUser($user->getUsername(), $user->generateHashPassword())) {     // insert to db
                  $system->sleepThree();
                  break;
                }
                $isTrueCreateAccount = false;
                $system->sleepThree();

              } catch (PDOException $e) {
                echo $e->getMessage();
                $system->sleepThree();
              }
      
            } else{
              echo "\nPassword and Confirmation Password don't match\n";
              $system->sleepThree();
            }
            break;
            
          default:
            echo "\nYou choose $optionCreateAccount.\nOption not available at the moment.";
            $system->sleepThree();
        }
      }
      break;

    case "2":   // login
      $isTrueLogin = true;
      while ($isTrueLogin) {
        $system->clearZeroWaiting();

        echo "\n\n------------------------Options------------------------";
        echo "\n1 - Login";
        echo "\n0 - Return\n";
  
        $optionLogin = readline();
        switch ($optionLogin) {
          case '0':   // login -> return
            $isTrueLogin = false;
            break;
  
          case '1':   // login -> login
            $system->clearZeroWaiting();

            echo "\nLogin\n";
            $username = readline('Insert your username: ');
            echo 'Insert your password: ';
            $password = Seld\CliPrompt\CliPrompt::hiddenPrompt();
  
            try {
              if (!$usersDB->authenticationUser($username, $password)) {      // authenticate user
                $system->sleepThree();
                break;
              }
              $isTrueLogin = false;

              $_SESSION['U'] = $sessionSecurity->encryptRSA($username);   // session username encrypted
              $_SESSION['P'] = $sessionSecurity->encryptRSA($password);   // session password encrypted

              $system->sleepOne();

              $isTrueAccount = true;
              while ($isTrueAccount) {
                $system->clearZeroWaiting();
                
                echo "\n\n------------------------Options------------------------";
                echo "\n1 - Account Information";
                echo "\n2 - Booking a trip";
                echo "\n3 - Show your booking information";
                echo "\n0 - Log out";
                echo "\nex - Exit\n";
  
                $optionAccount = readline();
                switch ($optionAccount) {
                  case '0':   // login -> account -> log out
                    unset($_SESSION['U']);
                    unset($_SESSION['P']);

                    echo "\nLogging out...";
                    $isTrueLogin = false;
                    $isTrueAccount = false;
                    $system->sleepThree();
                    break;

                  case 'ex':  // login -> account -> exit
                    unset($_SESSION['U']);
                    unset($_SESSION['P']);

                    $isTrue               = false;
                    $isTrueAccount        = false;
                    $isTrueLogin          = false;

                    echo "\nLogging out...";
                    $system->sleepOne();
                    echo "\nWe hope that you enjoy! 😎\n";
                    $system->clearThreeWaiting();
                    break;

                  case '1':   // login -> account -> account information
                    $system->clearZeroWaiting();

                    echo "\n\n------------------------Account information------------------------";

                    $userInfo = $usersDB->getInformationUser($sessionSecurity->decryptRSA($_SESSION['U']));
                    if (!$userInfo->isGetInfo) {
                      $system->sleepThree();
                      break;
                    }

                    echo "\n\nUsername: " . $sessionSecurity->decryptRSA($_SESSION['U']);
                    echo "\nPassword: " . str_repeat('*', strlen($sessionSecurity->decryptRSA($_SESSION['P'])) + 3);
                    echo "\nDate of creation: " . $userInfo->info['DateAccountCreation'];
                    echo "\nDate of last password modification: " . $userInfo->info['DatePasswordModification'];

                    echo "\n\n------------------------Options------------------------";
                    echo "\n1 - Change password\n";
                    echo "\nClick other button to return\n";

                    $optionAccountInfo = readline();
                    if ($optionAccountInfo == '1') {
                      echo 'Insert your password: ';
                      $password = Seld\CliPrompt\CliPrompt::hiddenPrompt();

                      try {
                        if (!$usersDB->authenticationUser($sessionSecurity->decryptRSA($_SESSION['U']),
                                            $password, $sessionSecurity)) {     // authenticate user
                          $system->sleepThree();
                          break;
                        }

                        echo "\nRequirements of password: ";
                        echo "\n- Minimum 8 characters in length";
                        echo "\n- Contains 3 of 4 of the following items:";
                        echo "\n  - Uppercase Letters";
                        echo "\n  - Lowercase Letters";
                        echo "\n  - Numbers";
                        echo "\n  - Symbols\n\n";

                        $newUser = new User();

                        echo 'Insert new password: ';
                        $newPassword = Seld\CliPrompt\CliPrompt::hiddenPrompt();

                        if (!$newUser->setPassword($password)) {
                          $system->sleepThree();
                          break;
                        }

                        echo 'Confirm new password: ';
                        $newPasswordConfirmation = Seld\CliPrompt\CliPrompt::hiddenPrompt();

                        if ($newPassword == $newPasswordConfirmation) {
                          $newUser->setUsername($sessionSecurity->decryptRSA($_SESSION['U']));
                          
                          if (!$usersDB->changePassword($newUser->getUsername(), $newUser->generateHashPassword())) {
                            $system->sleepThree();
                            break;
                          }

                          $_SESSION['P'] = $sessionSecurity->encryptRSA($newUser->getPassword());
                          $system->sleepThree();
                        } else {
                          echo "\nPasswords inserted don't match";
                          $system->sleepThree();
                          break;
                        }
                      } catch (PDOException $e) {
                        echo $e->getMessage();
                        $system->sleepThree();
                      }
                    }
                    break;

                  case '2':   // login -> account -> booking
                    $isTrueBookTrip = true;
                    while ($isTrueBookTrip) {
                      $system->clearZeroWaiting();

                      echo "\n\n------------------------Options------------------------";
                      echo "\n1 - Booking a trip";
                      echo "\n0 - Return\n";
  
                      $optionTrip = readline();
                      switch ($optionTrip) {
                        case '0':   // login -> account -> booking -> return
                          $isTrueBookTrip = false;
                          break;
                        
                        case '1':   // login -> account -> booking
                          $system->clearZeroWaiting();

                          echo "\n\n------------------------Choose your trip------------------------";
                          
                          $trip = new Trip();
  
                          echo "\nFrom: ";
                          $from = readline();
  
                          if (!$trip->setFrom($from)) {
                            $system->sleepThree();
                            break;
                          }
  
                          echo "\nTo: ";
                          $to = readline();
  
                          if (!$trip->setTo($to)) {
                            $system->sleepThree();
                            break;
                          }
  
                          echo "\nDate (format day/month/year || example => 02/09/2010): ";
                          $date = readline();
  
                          if (!$trip->setDate($date)) {
                            $system->sleepThree();
                            break;
                          }
  
                          // get trips available
                          $tripsReturned = $tripsDB->getTrips($trip);
                          if (!$tripsReturned->isGetTrips) {
                            $system->sleepThree();
                            break;
                          }

                          $system->clearThreeWaiting();
  
                          echo "\nTrips available\n";
                          foreach ($tripsReturned->trips as $eachTrip) {

                            $driverReturned = $tripsDB->getDriver($eachTrip['Driver']);
                            $busReturned = $tripsDB->getBus($eachTrip['Bus']);
                            
                            echo "\n\nID: " . $eachTrip['ID'];
                            echo "\nFrom: " . $eachTrip['From'];
                            echo "\nTo: " . $eachTrip['To'];

                            if ($busReturned->isGetBus) {
                              echo "\nBus: " . $busReturned->bus['Name'];
                            } else {
                              echo "\nBus: occurred an error 😞";
                            }
                            if ($driverReturned->isGetDriver) {
                              echo "\nDriver: " . $driverReturned->driver['Name'];
                            } else {
                              echo "\nDriver: occurred an error 😞";
                            }
                            echo "\nPassengers: " . $eachTrip['Passengers'];
                            echo "\nDate: " . $eachTrip['Date'];
                            echo "\nTime: " . $eachTrip['Time'];
                          }
  
                          echo "\n\nDo you want to book one of this trips?";
                          echo "\nInsert ----> y <---- if you want and any other to cancel: ";
                          $continueResponse = readline();
  
                          if ($continueResponse != 'y') {
                            $isTrueBookTrip = false;
                            break;
                          }
  
                          // book the trip
                          echo "\n\n------------------------Choose your trip------------------------";
                          echo "\nInsert the trip ID that you want to book: ";
                          $bookID = readline();
  
                          foreach ($tripsReturned->trips as $eachTrip) {
                            if ($bookID == $eachTrip['ID']) {
                              if (!$currentBookingsDB->makeBook($eachTrip, $sessionSecurity->decryptRSA($_SESSION['U']))) {
                                $system->sleepThree();
                                break;
                              }
                              $system->sleepThree();
                            }
                          }
                          break;
  
                        default:
                          echo "You choose $optionTrip.\nOption not available at the moment.";
                          $system->sleepThree();
                      }
                    }
                    break;

                  case '3':   // login -> account -> booking information
                    echo "\n\n------------------------Booking Information------------------------";

                    echo "\n\n------------------------Current Bookings------------------------";

                    $returnedCurrentBookings = $currentBookingsDB->getBookingByUser($sessionSecurity->decryptRSA($_SESSION['U']));
                    if (!$returnedCurrentBookings->isGetCurrentBookings) {
                      $system->sleepOne();
                    } else {
                      foreach ($returnedCurrentBookings->currentBookings as $eachBooking) {
                        echo "\n\nID: " . $eachBooking['ID'];
                        echo "\nTrip: " . $eachBooking['Trip'];
                        echo "\nDateTimeBooking: " . $eachBooking['DateTimeBooking'];
                      }
                    }

                    echo "\n\n------------------------Past Bookings------------------------";

                    $returnedPastBookings = $pastBookingsDB->getBookingByUser($sessionSecurity->decryptRSA($_SESSION['U']));
                    if (!$returnedPastBookings->isGetPastBookings) {
                      $system->sleepOne();
                    } else {
                      foreach ($returnedPastBookings->pastBookings as $eachBooking) {
                        echo "\n\nID: " . $eachBooking['ID'];
                        echo "\nTrip: " . $eachBooking['Trip'];
                        echo "\nDateTimeBooking: " . $eachBooking['DateTimeBooking'];
                      }
                    }
 
                    echo "\n\n------------------------Options------------------------";
                    echo "\nClick any button to return\n";

                    readline();
                    break;

                  default:
                    echo "You choose $optionAccount.\nOption not available at the moment.";
                    $system->sleepThree();
                }
              }
            } catch (PDOException $e) {
              echo $e->getMessage();
              $system->sleepThree();
            }
            break;
  
          default:
            echo "You choose $optionCreateAccount.\nOption not available at the moment.";
            $system->sleepThree();
        }
      }
      break;

    default:
      echo "You choose $option.\nOption not available at the moment.";
      $system->sleepThree();
  }
}

?>