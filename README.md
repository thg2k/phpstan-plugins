# ThGnet PHPStan plugins


## Compatibility

The current version is compatible with PHPStan v1.10 and v1.11.


## How to install

Copy the phar distribution file `phpstan-plugins.phar` into your project and add the following line into your config file:

    includes:
        - phar://phpstan-plugins.phar/conf/config.neon

Run the analysis specifying the phar file as additional autoload file:

    phpstan analyse -a phpstan-plugins.phar
