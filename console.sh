#!/bin/bash

# This script allows to run a sort-of interactive shell a-la rails
# the following env vars set the environment but must importantly whether
# we are running a console session so later we don't echo the output of the
# application but rather just do the usual requires
export SW0RDFISH_ENV="development"
export SW0RDFISH_CONSOLE="true"

php -a -d auto_prepend_file=app/Console.php
