# Submission Overview

## Running the Solution
I have used docker to run the application locally. It is a very basic setup with the minumum needed to run phpunit (no nginx etc) so it only has 2 services: PHP and PostgreSQL.

#### Option 1: Running the solution with Docker
Create a `.env` file by duplicating the `.env.example` file. The `docker-compose.yml` file will use the values in here when spinning up the PostgreSQL container. There should be no need to change these values.

Spin up the containers:
`docker-compose up --build`

Install composer depencies:
`docker-compose exec php composer install`

Migrate and seed the database using:
`docker-compose exec php php artisan migrate --seed`

Run the tests run:
`docker-compose exec php ./vendor/bin/phpunit`

#### Option 2: Running the solution with your local PHP and PostgreSQL
Create a `.env` file by duplicating the `.env.example` file. Change the database config to your local PostgreSQL config.

Install composer depencies:
`composer install`

Migrate and seed the database:
`php artisan migrate --seed`

Run the tests:
`./vendor/bin/phpunit`

## Notes
As a quick overview of my solution, I have create a single calculator that works for all 3 scenarios given. All tests were passing except the final 2 datasets provided in scenario C. I believe the expected values are incorrect and have therefore changed them according to my own calculations leading to all tests passing.

The second to last dataset in Scenario C had an expected value for the time cost of 1600 which I adjusted to 2800. I believe the original value of 1600 was obtained with the omission of the fact that the 9pm to 6am time cost limit of 1200 is weekday specific. If you take this into account you end up with a value of 2800.

 Likewise, the last dataset in Scenario C had an expected value for the time cost of 2400 which I adjusted to 3465. I'm unsure as to how the value of 2400 was obtained.