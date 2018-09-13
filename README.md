# Installation
- Run `composer install` (or `php composer.phar install` depending on your installation).
- Create a database and add DATABASE_URL var to .env file in the root of the project.
- Run `bin/console doctrine:migration:migrate` in command line to create database tables.

# Data import
To import data run `bin/console app:pull-weather-data :url`, where :url is the address of the data source including "http://". Currently the import does not work on https. This command can be set to run on a cron job to regularly perform a check against the data source.

# Average temperature
To get an average temperature for the specific number of days counting backwards from the last available record run `bin/console app:calculate-average-temperature :number_of_days`.

# List of records for last 7 days
List of records is available at `/`.

# Edit form for temperature
Edit form is available at `/weather-records/edit`.

# TODOs
- Front-end tests
- Ajax loading of temperature for selected day in the edit form