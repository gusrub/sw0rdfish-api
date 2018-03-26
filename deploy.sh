#!/bin/bash

LONG_COMMIT=$BITBUCKET_COMMIT
SHORT_COMMIT=${LONG_COMMIT:0:8}

if [[ ! -z "$BITBUCKET_BRANCH" ]]; then
        if [[ $BITBUCKET_BRANCH == "master" ]]; then
                echo "Will deploy commit '$SHORT_COMMIT' from $BITBUCKET_BRANCH branch to PRODUCTION environment ($APP_URL_PROD) in dreamhost..."
                sleep 3
        elif [[ $BITBUCKET_BRANCH == "staging" ]]; then
                echo "Will deploy commit '$SHORT_COMMIT' from $BITBUCKET_BRANCH branch to STAGING environment ($APP_URL_STAGING) in dreamhost..."
                sleep 3
        else
                echo "Will not deploy this commit ($SHORT_COMMIT) from branch '$BITBUCKET_BRANCH' since its not a tracked branch."
        fi
else
        echo "ENV var 'BITBUCKET_BRANCH' is not set! make sure it is for deployment to work."
        exit 1
fi
