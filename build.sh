#!/bin/bash

set -eu

echo -en "\n[+] Building phar file...\n"
php -d phar.readonly=0 $(which phar) pack \
  -f phpstan-plugins.phar \
  -l 0 \
  -c gz \
  -h md5 \
  -s stub.php \
  -i '^\.\/(conf|lib)\/|^\.\/bootstrap.php' \
  .

echo -en "\n[+] Fixing up phar timestamps...\n"
_git_ts=$(git show --no-patch --no-notes --pretty='%at')
./phar-fixup.php phpstan-plugins.phar "$_git_ts"

echo -en "\n[+] Generated file:\n"
md5sum phpstan-plugins.phar
