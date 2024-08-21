#!/usr/bin/env php
<?php

require __DIR__ . "/vendor/autoload.php";

$local_args = $argv;
$progname = array_shift($local_args);

$target = (string) array_shift($local_args);
$timestamp = (string) array_shift($local_args);

if (($target == "") || ($timestamp == "")) {
  fprintf(STDERR, "Usage: %s <file> <timestamp>\n", $progname);
  exit(1);
}

if (!preg_match('/^-?\d+$/', $timestamp)) {
  fprintf(STDERR,
      "Error: Invalid timestamp \"%s\", must be a valid UNIX timestamp\n",
      $timestamp);
  exit(1);
}

$dt = \DateTime::createFromFormat("U", $timestamp, new \DateTimeZone("UTC"));

$fixer = new \Seld\PharUtils\Timestamps($target);
$fixer->updateTimestamps($dt);
$fixer->save($target, \Phar::MD5);

printf("Updated timestamps for: %s\n", $target);
