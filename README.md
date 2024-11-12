# Product API Demo

A [Docker](https://www.docker.com/)-based  [Symfony](https://symfony.com) with an API to filter a list of products,
with [FrankenPHP](https://frankenphp.dev) and [Caddy](https://caddyserver.com/) inside

## Getting Started

1. If not already done, [install Docker Compose](https://docs.docker.com/compose/install/) (v2.10+)
2. Run `docker compose build --no-cache` to build fresh images
3. Run `docker compose up --pull always -d --wait` to set up and start a fresh Symfony project. The database will be filled with some demo data using symfony fixtures.
4. Open `https://localhost/products?category=boots` in POSTMAN or any other API client to see the API in action
5. Run `docker compose down --remove-orphans` to stop the Docker containers.

# Tools Inside

* PHPUnit: run `php bin/phpunit` to run unit tests. Tests inclue all business logic in all layers
* PHPStan: run `php bin/phpstan analyse src --level=8` to run static analysis. It's up to level 8 clear
* SWAGGER: you can access the API documentation at `/docs/swagger/product.yaml`

# Choices

* Clean Architecture: The project is organized in layers, with a clear separation of concerns.
* Money library: handling money is a serious matter in any company, and php may not be the best language to handle it. So I used the Money library to handle money operations. To avoid coupling it to the Product domain I used hexagonal architecture to wrap it into an adapter.
* Doctrine DBAL: I used Doctrine DBAL to handle the database operations. It's a good compromise between raw SQL and ORM, and it's significantly faster than ORM. Additionally, we avoid having to configure eager or lazy loading that could impact performance by retrieving unnecessary data in specific cases of use.
* Data Modeling: While the document stated that the product model had a specific structure containing a price object, I stored on the DB a simpler version of it to facilitate operations with DBAL and avoid the use of ORM. I queried the results in a single QUERY and showed the expected structure in the presentation layer using a toArray function. Ideally, this could be performed by a CQRS system.
* DataFixtures location is following symfony best practices. However many people could complain about this, so changing to the src directory could be a choice

# Assumptions:

* According to the task description: "this list could grow to have 20.000 products.". I added pagination to avoid getting long result sets that could saturate memory.
* According to the task description: "(optional) Can be filtered by priceLessThan" since the category filter doesn't have the optional tag I will assume its mandatory.

## Credits

The Original docker project was created by [Kévin Dunglas](https://dunglas.dev), co-maintained by [Maxime Helias](https://twitter.com/maxhelias) and sponsored by [Les-Tilleuls.coop](https://les-tilleuls.coop).
