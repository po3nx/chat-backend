
# Lumen Backend for Open AI Web ChatGPT

This project is a Lumen backend service designed to integrate with OpenAI's API. It provides a RESTful API that can be used for various web bot functionalities.

## Prerequisites

Before you begin, ensure you have the following installed:
- PHP >= 7.3
- OpenSSL PHP Extension
- PDO PHP Extension
- Mbstring PHP Extension
- Composer: Dependency Manager for PHP

## Configuration

To configure the application, you need to set up environment variables that the application will use. Start by copying the example environment file:

```shell
cp .env.example .env
```
Then, open the .env file and add your OpenAI API Key:

`OPENAI_API_KEY=YourAPIKeyHere`

## Installation

Follow these steps to install and set up the project:
1.  Install dependencies with Composer:
```shell
composer install
```
2.  Run the database migrations to create the necessary tables:
```shell
php artisan migrate
```
3.  Generate the application key (this is important for security, as it will be used for encryption):
```shell
php artisan key:generate
```
*Note: Ensure your database configuration details are correct in the .env file before running the migrations.*

## Running the Application

To start the Lumen development server, run:
```shell
php -S localhost:8000 -t .
```

This will start the server on http://localhost:8000, where you can access the API endpoints.

## Testing

To run the automated test suite, execute:
```shell
vendor/bin/phpunit
```
Ensure you have a testing environment set up in your .env file, typically using a different database connection to avoid affecting your development data.

### License

This project is open-sourced software licensed under the MIT license.

Remember to replace placeholders like `YourAPIKeyHere` with actual values. Moreover, depending on the specifics of your project, you might want to add sections like 'API Documentation', 'Docker Support', 'Troubleshooting', etc. If there are additional steps specific to your project, such as setting up a database or configuring a web server, be sure to include those as well.