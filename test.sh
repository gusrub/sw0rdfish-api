#!/bin/bash

export SW0RDFISH_ENV=test
vendor/bin/phpunit --colors=auto tests/
