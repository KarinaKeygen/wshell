#!/bin/bash

echo "* run entrypoint.sh"

HTTPDUSER=`ps axo user,comm | grep -E '[a]pache|[h]ttpd|[_]www|[w]ww-data|[n]ginx' | grep -v root | head -1 | cut -d\  -f1`

function symfony_cache_setfacl {
    rm -rf var/cache/* var/logs/* var/sessions/*
    setfacl -R -m u:"$HTTPDUSER":rwX -m u:`whoami`:rwX var
    setfacl -dR -m u:"$HTTPDUSER":rwX -m u:`whoami`:rwX var
}

# default symfony
cd /usr/share/nginx/symfony
symfony_cache_setfacl

# for wshell
cd /usr/share/nginx/wshell
symfony_cache_setfacl

# for citates
cd /usr/share/nginx/citates
symfony_cache_setfacl
