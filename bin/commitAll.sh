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

    echo
    cd $1
    for path in *; do
        echo "🟢 Commiting " $path
        cd $(realpath $path)
        git add . && git commit -m "$MESSAGE" && git push
        cd $1
        echo
    done
    cd $CURRENT_PATH
}

commitAllInPath $1 $2


# exemple
#    sauvagarder ce code source dans un fichier commitAll.sh (par exmple)
#   sh commitAll.sh /var/www/html "backup all html folder"
