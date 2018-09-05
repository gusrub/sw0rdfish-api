#!/bin/bash

export SW0RDFISH_ENV=development

BIND_PORT=80
BIND_ADDRESS="0.0.0.0"
ROOT_FOLDER="public"

while getopts ":a:p:f:" opt; do
    case "${opt}" in
        a)
            BIND_ADDRESS=${OPTARG}
            ;;
        p)
            BIND_PORT=${OPTARG}
            ;;
        f)
            ROOT_FOLDER=${OPTARG}
            ;;
        *)
            printf "\nInvalid option. Options are: -a [bind_address] -p [bind_port]  -f [root_folder]\n\n"
            exit 1
            ;;
    esac
done

php -S "$BIND_ADDRESS:$BIND_PORT" -t $ROOT_FOLDER
