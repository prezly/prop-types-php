COMPOSER_PHAR_VERSION = 1.8.6
COMPOSER_PHAR_CHECKSUM = b66f9b53db72c5117408defe8a1e00515fe749e97ce1b0ae8bdaa6a5a43dd542

vendor/autoload.php: composer.json composer.phar
	php composer.phar dump-autoload

composer.phar:
	curl https://getcomposer.org/download/$(COMPOSER_PHAR_VERSION)/composer.phar -o composer.phar.download
	echo "$(COMPOSER_PHAR_CHECKSUM)  composer.phar.download" | shasum -a 256 -c
	mv -f composer.phar.download composer.phar
