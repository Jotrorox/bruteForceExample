#!/bin/bash

# Export the current user and group IDs
export USER_ID=$(id -u)
export GROUP_ID=$(id -g)

# Run docker-compose with the current user/group IDs
docker compose up --build "$@" 