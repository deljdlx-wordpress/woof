commitAllInPath()
{
    CURRENT_PATH=$(pwd)

    echo
    cd $1
    for path in *; do
        echo "🟢 Commiting " $path
        cd $path
        git add . && git commit -m "$MESSAGE" && git push
        cd $1
        echo
    done
    cd $CURRENT_PATH
}

# ============================================================
CURRENT_BIN_PATH=$(pwd);

WOOF_PATH=$(realpath $CURRENT_BIN_PATH/..)
WOOF_PACKAGE_PATH=$(realpath $WOOF_PATH/woof-package)

PHI_PATH=$(realpath $WOOF_PACKAGE_PATH/phi)
WOOF_LIB_PATH=$(realpath $WOOF_PACKAGE_PATH/woof)

PLUGIN_PATH=$(realpath $WOOF_PATH/..)

WOOF_SCHEMA_PATH=$(realpath $PLUGIN_PATH/woof-schema-builder)


# ============================================================

if [ -z "$1" ]; then
    MESSAGE="Auto sync"

else
    MESSAGE=$1
fi


echo
echo "🔵 =======Commiting $PHI_PATH $1========="
commitAllInPath $PHI_PATH $MESSAGE

echo
echo "🔵 =======Commiting $WOOF_LIB_PATH========="
commitAllInPath $WOOF_LIB_PATH $MESSAGE

echo
echo "🔵 =======Commiting $WOOF_SCHEMA_PATH========="
cd $WOOF_SCHEMA_PATH
git add . && git commit -m "$MESSAGE" && git push
cd $CURRENT_BIN_PATH

echo
echo "🔵 =======Commiting $WOOF_PATH========="
cd $WOOF_PATH
git add . && git commit -m "$MESSAGE" && git push
cd $CURRENT_BIN_PATH


# ============================================================

# ls -al | grep -v -e '\.$' | awk '{print $9}'
# LIST=$(ls -al | grep -v -e '\.$' | awk '{print $9}') ;
# echo $LIST

# ls -al | grep -v -e '\.$' | awk '{print $9}' #| xargs cowsay #| xargs cd | pwd && ls -al && cd ..;

exit;
