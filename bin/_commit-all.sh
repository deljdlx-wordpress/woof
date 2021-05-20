cd ../woof-package/woof

echo "Commiting woof-theme"
cd  woof-theme
git add . && git commit -m "auto sync" && git push
cd ..
echo "------------------------------"

echo "Commiting woof-model"
cd woof-model
git add . && git commit -m "auto sync" && git push
cd ..
echo "------------------------------"


echo "Commiting woof-view"
cd woof-view
git add . && git commit -m "auto sync" && git push
cd ..
echo "------------------------------"

cd ../../bin

# ======================================================================
cd ../woof-package/phi

echo "Commiting phi-filesystem"
cd phi-filesystem
git add . && git commit -m "auto sync" && git push
cd ..
echo "------------------------------"

echo "Commiting phi-traits"
cd phi-traits
git add . && git commit -m "auto sync" && git push
cd ..
echo "------------------------------"

cd ../../bin

# ======================================================================
echo "Commiting woof"

git add . && git commit -m "auto sync" && git push



