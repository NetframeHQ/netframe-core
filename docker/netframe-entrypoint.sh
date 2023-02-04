#!/usr/bin/env bash

echo "nameserver 172.0.1.1" > /tmp/resolv.conf
cat /etc/resolv.conf | grep -v "nameserver" >> /tmp/resolv.conf

cat /tmp/resolv.conf > /etc/resolv.conf

exec /usr/local/bin/docker-php-entrypoint php-fpm
