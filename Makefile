# thgnet/phpstan-plugins

PHP = $(shell which php)
PHAR = $(shell which phar)

source_files := $(shell find conf lib -type f)
repo_ts := $(shell date -d`cut -f1 VERSION` +%s)
repo_version := $(shell cut -f2 VERSION)

all: phpstan-plugins.phar

.ts.$(repo_ts):
	@touch $@

phpstan-plugins.phar: .ts.$(repo_ts) vendor/seld/phar-utils \
		stub.php bootstrap.php $(source_files)
	@find . -maxdepth 1 -name ".ts.*" -not -name ".ts.$(repo_ts)" -delete
	@echo -en "\n[+] Building phar file...\n"
	@rm -f phpstan-plugins.phar
	@sed -e "s/@VERSION@/$(repo_version)/" stub.php > stub.php.tmp
	@$(PHP) -d phar.readonly=0 $(PHAR) pack \
		-f $@ \
		-l 0 -c gz -h md5 \
		-s stub.php.tmp \
		-i '^\.\/(conf|lib)\/|^\.\/bootstrap.php' .
	@echo -en "\n[+] Fixing up phar timestamps...\n"
	@./phar-fixup.php phpstan-plugins.phar $(repo_ts)
	@md5sum $@ > checksum.md5.tmp
	@if [ -d _generated ]; \
		then \
			xhash=`cat checksum.md5.tmp | cut -d' ' -f1`; \
			cp -a phpstan-plugins.phar _generated/$(repo_ts)-$$xhash.phar; \
		fi;
	@if cmp -s checksum.md5 checksum.md5.tmp; \
		then \
			echo -en "\n[+] Generated expected product file:\n"; \
			cat checksum.md5.tmp; \
		else \
			echo -en "\n[!] Generated NEW product file:\n"; \
			diff -uN checksum.md5 checksum.md5.tmp; \
			cp checksum.md5.tmp checksum.md5; \
		fi;
	@rm -f checksum.md5.tmp stub.php.tmp

distclean:
	git clean -xdf

clean:
	rm -rf .phpstan.cache
	rm -f phpstan-plugins.phar .ts.*

.PHONY: clean
