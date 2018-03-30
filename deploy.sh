#!/bin/bash

echo "Listing files"
ls -lh

LONG_COMMIT=$BITBUCKET_COMMIT
SHORT_COMMIT=${LONG_COMMIT:0:8}
BUILD_FILE=$SHORT_COMMIT.tar.bz2

function compress_build {
    echo "Compressing $BUILD_FILE"

    tar -cjf $BUILD_FILE \
        app/ \
        public/ \
        vendor/ \
        LICENSE.md \
        sw0rdfish-sqlite.sql \
        sw0rdfish-mysql.sql \
        README.md
    echo $SHORT_COMMIT.tar.bz2
}

function upload_build {
    APP_URL=$1
    ENVIRONMENT=$2
    echo "Uploading $BUILD_FILE to $APP_URL"
    ssh $DEPLOY_USER@$APP_URL "mkdir -p builds/$ENVIRONMENT"
    scp $BUILD_FILE $DEPLOY_USER@$APP_URL:./builds/$ENVIRONMENT/$BUILD_FILE
}

function activate_build {
    APP_URL=$1
    ENVIRONMENT=$2
    echo "Activating release $SHORT_COMMIT"
    BUILD_FOLDER=$(echo $BUILD_FILE | cut -d'.' -f 1)
    ssh $DEPLOY_USER@$APP_URL APP_URL=$APP_URL BUILD_FILE=$BUILD_FILE BUILD_FOLDER=$BUILD_FOLDER ENVIRONMENT=$ENVIRONMENT 'bash -s'  <<-ENDSSH
    echo "Going to builds/$ENVIRONMENT folder..."
    cd builds/$ENVIRONMENT
    rm -Rf $BUILD_FOLDER
    mkdir $BUILD_FOLDER
    echo "Extracting $BUILD_FILE to $BUILD_FOLDER..."
    tar xjf $BUILD_FILE -C $BUILD_FOLDER
    echo "Removing $BUILD_FILE..."
    rm -Rf $BUILD_FILE
    echo "Linking $BUILD_FOLDER to be the current version..."
    rm -Rf current
    ln -sv $BUILD_FOLDER current
    echo "Removing old $APP_FOLDER link..."
    rm -Rf ../../$APP_URL
    echo "Copying env var file for apache..."
    cat ~/htaccess.$ENVIRONMENT > current/public/.htaccess
    echo "Linking new current version to $APP_URL..."
    ln -sv \$(pwd)/current/public ../../$APP_URL
ENDSSH
}

if [[ ! -z "$BITBUCKET_BRANCH" ]]; then
    if [[ $BITBUCKET_BRANCH == "master" ]]; then
        echo "Will deploy commit '$SHORT_COMMIT' from $BITBUCKET_BRANCH branch to PRODUCTION environment ($APP_URL_PROD) in dreamhost..."
        compress_build
        upload_build $APP_URL_PROD "production"
        activate_build $APP_URL_PROD "production"
    elif [[ $BITBUCKET_BRANCH == "staging" ]]; then
        echo "Will deploy commit '$SHORT_COMMIT' from $BITBUCKET_BRANCH branch to STAGING environment ($APP_URL_STAGING) in dreamhost..."
        compress_build
        upload_build $APP_URL_STAGING "staging"
        activate_build $APP_URL_STAGING "staging"
    else
        echo "Will not deploy this commit ($SHORT_COMMIT) from branch '$BITBUCKET_BRANCH' since its not a tracked branch."
    fi
else
    echo "ENV var 'BITBUCKET_BRANCH' is not set! make sure it is for deployment to work."
    exit 1
fi
