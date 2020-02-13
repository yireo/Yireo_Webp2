#!/usr/bin/env bash
if [ "$EUID" -ne 0 ] ; then
    sudo service postfix stop
else
    service postfix stop
fi

echo # print a newline
smtp-sink -d "%d.%H.%M.%S" localhost:2500 1000 &
echo 'sendmail_path = "/usr/sbin/sendmail -t -i "' > ~/.phpenv/versions/$(phpenv version-name)/etc/conf.d/sendmail.ini
