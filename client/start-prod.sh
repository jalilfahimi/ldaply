#!/bin/bash
rm -rf node_modules
npm ci
ng build 
service apache2 reload
apache2ctl -D FOREGROUND