#!/bin/sh

# Modified by: Ulrich Block <ulrich.block@easy-wi.com>
# We need the $@ to be able to send commands to the server binary

cd "$(dirname "$0")"

LD_LIBRARY_PATH=${LD_LIBRARY_PATH}:./ ./starbound_server $@
