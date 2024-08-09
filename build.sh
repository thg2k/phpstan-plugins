#!/bin/bash

php -d phar.readonly=0 $(which phar) pack \
  -f phpstan-plugins.phar \
  -l 0 \
  -c gz \
  -h md5 \
  -s stub.php \
  -i '^\.\/(conf|lib)\/|^\.\/bootstrap.php' \
  .
