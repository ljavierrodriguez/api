[<-- back to main readme ](./README.md)

# ![alt text](https://assets.breatheco.de/apis/img/images.php?blob&random&cat=icon&tags=breathecode,32) BreatheCode API
# Project Structure

Most of the code is written inside the /api/src/ directory:

- Emails: Email templates used
- Handlers: Contain the actual code that is execured when the endpoint is called
- Helpers: a bunch of useful classes for emailing, validations, etc.
- Middleware: read only, does stuff like error handling.
- Routes: The endpoints.
- Migrations: all the migration files available.
- Seeds: Seeders to fill the database with mock content, there are not many seeds.
- Tests: Unit tests

## Arquitechture

- The project is mainly based on [Slim PHP](https://www.slimframework.com/) and the routes are being defined on /api/src/routes
- For the model we are using [The Eloquent ORM](https://laravel.com/docs/5.0/eloquent).
- For migrations we are usin [Phinx](https://phinx.org/) and they are located in /api/src/migrations
- For sending emails: [Amazon Simple Email Service](https://aws.amazon.com/ses/) and the tempaltes are in /api/src/emails/
- For testing we are using PHPUnit and the tests are located at /src/tests


