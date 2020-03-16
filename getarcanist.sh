#!/bin/bash
set -e

INSTALL_SCRIPT="/tmp/installarcanist.sh"

curl -L https://raw.githubusercontent.com/diablomedia/avt-arcanist/stable-branch/installarcanist.sh -o $INSTALL_SCRIPT
/bin/bash $INSTALL_SCRIPT
rm $INSTALL_SCRIPT
