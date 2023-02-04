#!/usr/bin/env sh

set -e

if [ "${2}" = "" ]; then
  echo "You must give a directory path"
  exit 1
fi

init_dir() {
  mkdir -p "${1}"
  chown -R "${USER}:${GROUP}" "${1}"
  chmod -R "${2:-757}" "${1}"
}

lock_dir() {
  chown -R "${USER}:${GROUP}" "${1}"
  chmod -R "${2}" "${1}"
}

if [ "${1}" = "init" ]; then
  init_dir "${2}"
elif [ "${1}" = "lock" ]; then
  lock_dir "${2}" "${3:-755}"
elif [ "${1}" = "clean" ]; then
  rm -rf "${2}"
elif [ "${1}" != "" ]; then
  echo "Uknown command: ${1}"
  exit 2
else
  echo "No command given"
  exit 3
fi
