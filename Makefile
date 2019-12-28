COMPOSER_PHAR_VERSION = 1.8.6
COMPOSER_PHAR_CHECKSUM = b66f9b53db72c5117408defe8a1e00515fe749e97ce1b0ae8bdaa6a5a43dd542

.PHONY: test

test: vendor
	vendor/bin/phpunit

vendor: composer.json
	composer install
