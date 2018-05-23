#!/bin/bash

export SW0RDFISH_ENV=test
ARGS=$@

if [[ $ARGS == "" ]]; then
	echo "No arguments given... running full suite"
	vendor/bin/phpunit --colors=auto --verbose tests/
else
	echo "Running specific tests..."
	vendor/bin/phpunit --colors=auto --verbose $ARGS
fi
