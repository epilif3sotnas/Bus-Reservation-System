# Changelog

## [v1.1.0](https://github.com/epilif3sotnas/Bus-Reservation-System/releases/tag/v1.1.0)

Database

- Change: Users db dates from PostgreSQL dates to php dates ISO 8601
- Fix: Display a message to inform that there isn't any data retrieved from DB
- Fix: Don't exit if didn't retrieve any data on current and past bookings
- Fix: Check if there are space when making a trip book
- Fix: Add a passenger when a book is done

Account

- Add: Change password in account information
- Add: Delete account option
- Add: Option delete book trip

Dependencies

- Add: phpseclib/phpseclib
- Add: seld/cli-prompt

Security

- Add: Password requirements
- Change: Encrypt session data
- Change: Hide password input
- Fix: Delete all database or system message

User experience

- Change: Hide last inputs and outputs
- Fix: echo messages to users
- Fix: Only display trips that aren't full
- Fix: Use trim to delete whitespaces from user inputs

Code quality

- Change: OOP classes (getters and setters)
- Change: Check if interaction with DB has errors

Improve efficiency

- Change: Delete redundant code

## [v1.0.1](https://github.com/epilif3sotnas/Bus-Reservation-System/releases/tag/v1.0.1)

Fixes:

- Change from '' to "" on echo

## [v1.0.0](https://github.com/epilif3sotnas/Bus-Reservation-System/releases/tag/v1.0.0)

- Inicial realease