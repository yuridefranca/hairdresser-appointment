#!/usr/bin/env bash

export $(grep -v '^#' .env | xargs)

while getopts r: argument; do
	case "${argument}" in
	r) remove=${OPTARG} ;;
	esac
done

function removeDependencies() {
	sudo rm -rf www/node_modules
	sudo rm -rf www/vendor
}

function removeDockerImage() {
	docker rmi $(docker images -q "$PROJECT_NAME/*")
}

docker-compose down -v

if [ "$remove" == "dependencies" ]; then
	removeDependencies
elif [ "$remove" == "image" ]; then
	removeDockerImage
elif [ "$remove" == "all" ]; then
	removeDockerImage
	removeDependencies
fi