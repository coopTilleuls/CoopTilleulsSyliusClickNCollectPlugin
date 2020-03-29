# Contributing

## Documentation

For a comprehensive guide on Sylius Plugins development please go to Sylius documentation,
there you will find the [Plugin Development Guide](https://docs.sylius.com/en/latest/plugin-development-guide/index.html),
that is full of examples.

## Quickstart Installation

1. Run `composer install`.

2. From the root directory, run the following commands:

       $ (cd tests/Application && yarn install)
       $ (cd tests/Application && yarn build)
       $ (cd tests/Application && bin/console assets:install public -e test)

       $ (cd tests/Application && bin/console doctrine:database:create -e test)
       $ (cd tests/Application && bin/console doctrine:schema:create -e test)
       
To be able to setup a plugin's database, remember to configure you database credentials in `tests/Application/.env` and `tests/Application/.env.test`.

## Usage

### Preparing the Test Environment

    $ bin/console doctrine:database:create -e panther
    $ bin/console doctrine:schema:create -e panther
    $ bin/console assets:install --symlink -e panther
    $ bin/console sylius:fixtures:load -e panther

### Running Plugin Tests

    $ vendor/bin/phpunit

### Opening Sylius with Your Plugin

* Using `test` environment:

       $ (cd tests/Application && bin/console sylius:fixtures:load -e test)
       $ (cd tests/Application && bin/console server:run -d public -e test)
    
* Using `dev` environment:

       $ (cd tests/Application && bin/console sylius:fixtures:load -e dev)
       $ (cd tests/Application && bin/console server:run -d public -e dev)
