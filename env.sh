#!/bin/bash
set_env () {
  cp docker-compose-"$1".yml docker-compose.yml
  # server
  cp server/.env-"$1" server/.env
  # client
  cp client/start-"$1".sh client/start.sh
  cp client/Dockerfile-"$1" client/Dockerfile
  cp client/src/config-"$1".ts client/src/config.ts

}

if [ "$1" == "production" ] || [ "$1" = "prod" ]; then
  echo "Changing to production mode"
  set_env 'prod'
elif [ "$1" == "development" ] || [ "$1" = "dev" ]; then
  echo "Changing to development mode"
  set_env 'dev'
else
  echo "ERR: Unknown value for environment parameter."
  echo "
USAGE:
  env.sh [ARGS]

ARGS:
  prod, production
      moves prepared production configuration files to the appropriate places

  dev, development
      moves prepared development configuration files to the appropriate places
"
fi
