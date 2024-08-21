
all: phpstan-plugins.phar

phpstan-plugins.phar: stub.php bootstrap.php conf $(shell find conf lib -type f)
	./build.sh

clean:
	rm -rf .phpstan.cache
	rm -f phpstan-plugins.phar

.PHONY: clean
