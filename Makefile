# thgnet/phpstan-plugins

PHP = $(shell which php)
PHAR = $(shell which phar)

source_files := $(shell find conf lib -type f)
git_ts := $(shell git show --no-patch --no-notes --pretty='%at')

all: phpstan-plugins.phar

.ts.$(git_ts):
	@touch $@

phpstan-plugins.phar: stub.php bootstrap.php $(source_files) .ts.$(git_ts)
	@find . -maxdepth 1 -name ".ts.*" -not -name ".ts.$(git_ts)" -delete
	@echo -en "\n[+] Building phar file...\n"
	@$(PHP) -d phar.readonly=0 $(PHAR) pack \
		-f $@ \
		-l 0 -c gz -h md5 \
		-s stub.php \
		-i '^\.\/(conf|lib)\/|^\.\/bootstrap.php' .
	@echo -en "\n[+] Fixing up phar timestamps...\n"
	@./phar-fixup.php phpstan-plugins.phar $(git_ts)
	@echo -en "\n[+] Generated file:\n"
	@md5sum $@

clean:
	rm -rf .phpstan.cache
	rm -f phpstan-plugins.phar .ts.*

.PHONY: clean
