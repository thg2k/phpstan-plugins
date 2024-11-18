#!/bin/sh

current_version="$(cut -f2 VERSION)"

if [ -z "$1" ]
then
  echo "Usage: $0 <version>"
  echo ""
  echo "Current version: $current_version"
  echo ""
  exit 1
fi

new_version="$1"
new_timestamp="$(date --utc +'%Y-%m-%dT%H:%M:%SZ')"

if [ "$current_version" == "$new_version" ]
then
  echo ""
  echo "Current version: $new_version (updated timestamp)"
  echo ""
else
  echo ""
  echo "Updated version: $current_version => $new_version"
  echo ""
fi

echo -e "$new_timestamp\t$new_version" > VERSION
