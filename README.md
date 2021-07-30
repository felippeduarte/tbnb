## Requirements

- PHP 7.4
- SQL Database

## Features

- Create a new stock symbol: Click "Add New on Top Right"
- Delete a stock symbol: Click on trash button right to the stock
- Update the stock price for a given day: Click on $ button right to the stock
- Display a chart of any combination of chosen stock prices in the most recent 30 days: Click Symbol on stock list
- Bulk update feature (enter multiple stock prices for a given day): top link Update Quotes
- "simulated price" feature: top link Update Quotes

## How to run

- git clone
- create .env file with DB credentials
- php artisan serve
- access localhost:8000

## How to test

- create a database for tests named tbnb_testing
- run "php artisan test"