#!/bin/bash


usage()
{

    cat <<EOF
Build a docker image and environment file to fill a docker-compose parameters ( packaging/docker/docker.compose )
with this dev project linked on ldap conf file, html content and includes.

Requires:
  - sudo rights for docker build
  - internet access to collect docker images and packages during docker build


arguments:

    h|-h|help|--help : display this help

Run it without arguments.
EOF

}

# WARNING use parent directory as application name
application=$(basename $(pwd))
timestamp="$(date +"%Y%m%d%H%M%S")"
image_basename=${application}.$timestamp

CONFIG_DIR=$HOME/worteks/configs/${application}
LOG_DIR=$HOME/worteks/logs/${application}

project=.

project_dir=$(pwd)/$project

env_dir=$project_dir/env

# input template
template_env=$env_dir/template.env
# output env file for docker compose
docker_env=$env_dir/${application}.env

while [[ $# > 0 ]]
do
    case $1 in
        h|-h|help|--help)
            usage
            exit 1
            ;;
        tag_name=*)
            tag_name=${1/tag_name=/}
            ;;
        *)
            echo "[ERROR] Unrecognized argument '$1'" >&2
            usage
            exit 1
            ;;
    esac
    shift
done




if [[ -z $tag_name ]]
then
    # use git branch as tag name if in git
    if [[ -d .git ]] && which git >/dev/null
    then
        tag_name=$(git branch --show-current)
    else
        tag_name=last
    fi
fi

image_name=$image_basename:$tag_name

read -p "build $image_name ? (Y/N/Skip) :" YesNoSkip

if [[ $YesNoSkip =~ [S][kip]* ]]
then
    echo "[INFO] Skip build" >&2
    LTB_IMAGE=$(<.last_build)
else
    if [[ $YesNoSkip =~ [Y][es]* ]]
    then
        # ?????????? TWICE ???
        # sudo docker build -t "$image_name" -f packaging/docker/Dockerfile .
        # echo -e "You can follow build with ${green}tail -f  build.log${reset} in another console"
        sudo docker build -t "$image_name" -f $project/packaging/docker/Dockerfile $project
        # output to stderr and other shenanigans...
        # LTB_IMAGE=$( | tee build.log | grep 'Successfully built' |awk '{print $3;}')
    else
        echo "[INFO] build not accepted by user" >&2
        exit 1
    fi

    LTB_IMAGE=$(sudo docker image ls | grep "$image_basename"  |awk '{print $3;}')
    echo "docker image $image_basename LTB_IMAGE=$LTB_IMAGE"

    if [[ -z $LTB_IMAGE ]]
    then
        echo "[FATAL] docker build failed. no LTB_IMAGE image set. See ./build.log to find root cause" >&2
        exit 1
    else
        echo "$LTB_IMAGE" >.last_build
    fi
fi

if [[ -f $template_env ]]
then
    # $ldap_url in config.inc.local.dev.php, could be autoextracted.
    LDAP_HOST='ldap://host.docker.internal:1089'
    HTDOCS=$project_dir/htdocs
    INCLUDES=$project_dir/includes
    TEMPLATES=$project_dir/templates

    while read -r line
    do
        if [[ $line =~ LTB_IMAGE=\$ ]]
        then
            echo "LTB_IMAGE=$LTB_IMAGE"
        elif [[ $line =~ LDAP_HOST=\$ ]]
        then
            echo "LDAP_HOST=$LDAP_HOST"
        elif [[ $line =~ HTDOCS=\$ ]]
        then
            echo "HTDOCS=$HTDOCS"
        elif [[ $line =~ INCLUDES=\$ ]]
        then
            echo "INCLUDES=$INCLUDES"
        elif [[ $line =~ CONFIG_DIR=\$ ]]
        then
            echo "CONFIG_DIR=$CONFIG_DIR"
        elif [[ $line =~ LOG_DIR=\$ ]]
        then
            echo "LOG_DIR=$LOG_DIR"
        elif [[ $line =~ TEMPLATES=\$ ]]
        then
            echo "TEMPLATES=$TEMPLATES"
        else
            echo $line
        fi
    done <$template_env >$docker_env

    echo "[INFO] generated environment file for docker-compose is $docker_env" >&2
else
    echo "[WARNING] no  $template_env to generate $docker_env" >&2
fi
