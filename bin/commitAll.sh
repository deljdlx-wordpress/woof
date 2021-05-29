# arguments :
# - dossier dans lequel nous allons commiter tous les sous dossiers
# - optionnel : message de commit (par défaut "Auto sync")


commitAllInPath()
{
    CURRENT_PATH=$(pwd)

    if [ -z "$2" ]; then
        MESSAGE="Auto sync"

    else
        MESSAGE=$2
    fi

    START_PATH=$(realpath $1)

    echo "============================================"
    echo "🔵Commiting all in " $START_PATH
    echo "============================================"

    echo
    cd $START_PATH
    for path in *; do
        path=$(realpath $path)
        if [ -d $path ]; then
            echo "🟢==== Commiting " $path "====="
            cd $path
            git add . && git commit -m "$MESSAGE" && git push
            cd $START_PATH
            echo
        else
            echo "⚠️==== Skipping " $path "====="
        fi
    done
}

commitAllInPath $1 $2


# exemple
#   sauvagarder ce code source dans un fichier commitAll.sh (par exmple)
#   sh commitAll.sh /var/www/html "backup all html folder"
