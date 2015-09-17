#!/bin/bash
set -e

# Downloads arcanist, libphutil, etc and configures your system

if [ -z "$ARC_LOC_DIR" ]; then
    ARC_LOC_DIR="/usr"
fi

if [ -z "$ARC_BIN_DIR" ]; then
    ARC_BIN_DIR="$ARC_LOC_DIR/bin"
fi

if [ -z "$ARC_ARC_PHP_DIR" ]; then
    ARC_PHP_DIR="$ARC_LOC_DIR/share/php"
fi

if [ ! -w "$ARC_LOC_DIR" ]; then
    if [ -z "$SUDO_USER" ]; then
        echo "Re-running installation with sudo (no permission on $ARC_LOC_DIR for current user)."
        exec sudo /bin/sh $0 $*
    else
        echo "We can't seem to access ${ARC_LOC_DIR}. Please check permissions on this folder and try again."
        exit -1
    fi;
fi;

if [ ! -e "$ARC_PHP_DIR" ]; then
    mkdir -p $ARC_PHP_DIR
fi;

# Install or update libphutil
echo "Updating libphutil.."
if [ -e "$ARC_PHP_DIR/libphutil" ]; then
    $ARC_BIN_DIR/arc upgrade
else
    git clone git://github.com/facebook/libphutil.git "$ARC_PHP_DIR/libphutil"
    git clone git://github.com/facebook/arcanist.git "$ARC_PHP_DIR/arcanist"
    #git clone git://github.com/facebook/phabricator.git "$ARC_PHP_DIR/phabricator"
fi

# Install or update libavt
echo "Updating libavt.."
if [ -e "$ARC_PHP_DIR/libavt" ]; then
    cd "$ARC_PHP_DIR/libavt" && git pull origin master
else
    git clone git://github.com/diablomedia/avt-arcanist.git "$ARC_PHP_DIR/libavt"
fi

# Register arc commands
echo "Registering arc commands.."

## create-arcconfig
ln -fs "$ARC_PHP_DIR/libavt/bin/create-arcconfig" "$ARC_BIN_DIR/create-arcconfig"
chmod +x "$ARC_BIN_DIR/create-arcconfig"

## update-arcanist
ln -fs "$ARC_PHP_DIR/libavt/bin/update-arcanist" "$ARC_BIN_DIR/update-arcanist"
chmod +x "$ARC_BIN_DIR/update-arcanist"

## arc
ln -fs "$ARC_PHP_DIR/libavt/bin/arc" "$ARC_BIN_DIR/arc"
chmod +x "$ARC_BIN_DIR/arc"

echo "Done!"
