## Requirements

- PHP
- MySQL
- Composer

## Installation

- Clone the repo (git clone git@github.com:brestrepo-test/advisor_engine.git advisor_engine)
- cd advisor_engine
- composer install
    - In this step you will be prompted to configure your database access
- php bin/console doctrine:schema:update --force
- php bin/console server:run
- Open a browser and goto http://127.0.0.1:8000

## Timesheet

### Tasks

- Initial setup - ~ 1/2 hour
- Create models for Transaction/Stock/UserSummary - ~ 1/2 hour
- Update Stock price service - ~ 1 hour
- Transaction crud - ~ 1 hour
- User portfolio service - ~ 1 hour

Remaining effort - ~ 1 hour
- As a user I should be able to see all the stock in my portfolio
- As a user I should be able to see a summary of my transaction
- As a user I should be able the list of all my transactions
- As a user I should be able to add a buy transaction
- As a user I should be able to add a sell transaction
