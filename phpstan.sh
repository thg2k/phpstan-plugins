#!/bin/bash

./vendor/bin/phpstan analyse \
  --memory-limit 512M \
  -c .phpstan.neon \
  "$@"
