#!/bin/bash

# A SHELL SCRIPT TO MANAGE SYMLINKS TO SETTINGS FILES

RED='\e[1;31m'
BLUE='\e[1;36m'
GREEN='\e[1;32m'
UNSET='\e[0m'

if [ "$1" = "" ]
then
    echo "";
    echo -e "${RED}This script creates all the required symlinks from the subfolder configurations." ;
    echo "";
    echo -e "${GREEN}Usage: ./symsync.sh name_of_environment" ;
    echo "";
    echo -e "Examples: ";
    echo -e "./symsync.sh dev  - This will create symlinks from the folder configurations/dev" ;
    echo -e "./symsync.sh live - This will create symlinks from the folder configurations/live${UNSET}" ;
    exit
fi

SRC="configurations/$1"
DEST="."

mkdir -p $DEST

if [ ! -f .gitignore ] ;
    then touch .gitignore ;
fi

## 1 - recreate SRC directory tree as DEST
find $SRC -type d |while read PATHNAME; do
    mkdir -p "$DEST${PATHNAME#$SRC}" ;
done

## 2 - create a a symlink under $DEST for each file under $SRC
find $SRC -type f |while read PATHNAME; do
    NEW="$DEST${PATHNAME#$SRC}" ;
    NEWGIT="${PATHNAME#$SRC}" ;
    if [ -L "$NEW" ]
    then echo -e "${GREEN}Removing old symlink to${UNSET} $NEW" ;
        rm -f "$NEW" ;
    fi

    echo -e "${GREEN}Creating new symbolic link to${UNSET} $NEW" ;
    ln -s "$PWD/$PATHNAME" "$NEW" ;

#    grep -xq $NEWGIT .gitignore ;
#    if [ $? -eq 0 ] ; then
#        echo -e "$NEWGIT ${BLUE}is already added to .gitignore${UNSET}" ;
#    else
#        echo -e "${BLUE}Adding ${UNSET}$NEWGIT${BLUE} to .gitignore${UNSET}" ;
#        echo $NEWGIT >> .gitignore ;
#    fi
#    echo "-------------------------------------------------------";
done

SRC="configurations/global"

## 1 - recreate SRC directory tree as DEST
find $SRC -type d |while read PATHNAME; do
    mkdir -p "$DEST${PATHNAME#$SRC}" ;
done

## 2 - create a a symlink under $DEST for each file under $SRC
find $SRC -type f |while read PATHNAME; do
    NEW="$DEST${PATHNAME#$SRC}" ;
    NEWGIT="${PATHNAME#$SRC}" ;
    if [ -L "$NEW" ]
    then echo -e "${GREEN}Removing old symlink to${UNSET} $NEW" ;
        rm -f "$NEW" ;
    fi

    echo -e "${GREEN}Creating new symbolic link to${UNSET} $NEW" ;
    ln -s "$PWD/$PATHNAME" "$NEW" ;

#    grep -xq $NEWGIT .gitignore ;
#    if [ $? -eq 0 ] ; then
#        echo -e "$NEWGIT ${BLUE}is already added to .gitignore${UNSET}" ;
#    else
#        echo -e "${BLUE}Adding ${UNSET}$NEWGIT${BLUE} to .gitignore${UNSET}" ;
#        echo $NEWGIT >> .gitignore ;
#    fi
#    echo "-------------------------------------------------------";
done
