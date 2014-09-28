#!/bin/bash

SCRIPT_DIR=$(cd $(dirname $0); pwd -P)
ARCH=$(arch)
BIN="/opt/webpanel/lamp-$ARCH/php/bin"
PHP="$BIN/php"

PATH="$BIN:$SCRIPT_DIR:$PATH"
export PATH

exec "$@"

