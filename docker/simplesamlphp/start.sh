#!/bin/bash
service postfix restart
service cron start
apache2ctl -D FOREGROUND

