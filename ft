#!/bin/sh

# find php: pear first, command -v second, straight up php lastly
PHP_BIN=php

# find ft.php: pear first, same directory 2nd, 
if test "@php_dir@" != '@'php_dir'@'; then
    PHP_DIR="@php_dir@"
else
    SELF_LINK="$0"
    SELF_LINK_TMP="$(readlink "$SELF_LINK")"
    while test -n "$SELF_LINK_TMP"; do
        SELF_LINK="$SELF_LINK_TMP"
        SELF_LINK_TMP="$(readlink "$SELF_LINK")"
    done
    PHP_DIR="$(dirname "$SELF_LINK")"
fi

"$PHP_BIN" -d safe_mode=Off -f "$PHP_DIR/ft.php" -- "$@"