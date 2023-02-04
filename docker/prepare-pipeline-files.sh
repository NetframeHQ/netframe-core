#!/usr/bin/env sh

# `docker run -it` is impossible because device is not a TTY
sed -i '/docker run/s/ -it//g' Makefile

# prevent using another user than root
sed -i '/docker run/s/ -u node//g' Makefile
