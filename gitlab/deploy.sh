#! /bin/bash
NAME=$1
if [ ! -d "${WEB_SRC}/src" ]; then
        mkdir ${WEB_SRC}/src
fi

if [ ! -d "${WEB_SRC}/${NAME}" ]; then
        cd $LOCAL_SRC/src
        wget https://wordpress.org/latest.zip
        unzip latest.zip
        cp -R  wordpress "${WEB_SRC}/${NAME}"
        chmod 777 "${WEB_SRC}/${NAME}"
        chmod -R 777 "${WEB_SRC}/${NAME}/wp-content"
        rm -rf wordpress
        rm -rf latest.zip
fi

if [ ! -d "${WEB_SRC}/${NAME}dev" ]; then
        cd $LOCAL_SRC/src
        wget https://wordpress.org/latest.zip
        unzip latest.zip
        cp -R wordpress "${WEB_SRC}/${NAME}dev"
        chmod 777 "${WEB_SRC}/${NAME}dev"
        chmod -R 777 "${WEB_SRC}/${NAME}dev/wp-content"
        rm -rf wordpress
        rm -rf latest.zip
fi
if [ ! -L "${WEB_SRC}/${NAME}/wp-content/themes/${NAME}" ]; then
        ln -s $LOCAL_SRC/${NAME}/code/theme ${WEB_SRC}/${NAME}/wp-content/themes/${NAME}
fi

if [ ! -L "${WEB_SRC}/${NAME}dev/wp-content/themes/${NAME}" ]; then
        ln -s $LOCAL_SRC/${NAME}dev/code/theme ${WEB_SRC}/${NAME}dev/wp-content/themes/${NAME}
fi

for PLUGIN_NAME in ${LOCAL_SRC}/${NAME}/code/plugin/*/
do
    PLUGIN_NAME=${PLUGIN_NAME%*/}
    PLUGIN_NAME=${PLUGIN_NAME##*/}

        if [ ! -L "${WEB_SRC}/${NAME}/wp-content/plugins/${PLUGIN_NAME}" ]; then
			ln -s "${LOCAL_SRC}/${NAME}/code/plugin/${PLUGIN_NAME}" "${WEB_SRC}/${NAME}/wp-content/plugins/${PLUGIN_NAME}"
        fi
done

for PLUGIN_NAME in ${LOCAL_SRC}/${NAME}dev/code/plugin/*/
do
    PLUGIN_NAME=${PLUGIN_NAME%*/}
    PLUGIN_NAME=${PLUGIN_NAME##*/}

        if [ ! -L "${WEB_SRC}/${NAME}dev/wp-content/plugins/${PLUGIN_NAME}" ]; then
			ln -s "${LOCAL_SRC}dev/${NAME}/code/plugin/${PLUGIN_NAME}" "${WEB_SRC}/${NAME}dev/wp-content/plugins/${PLUGIN_NAME}"
        fi
done

