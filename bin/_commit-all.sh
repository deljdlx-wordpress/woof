cd ..

echo "Commiting woof-theme"
cd  woof-package/woof-theme
git add . && git commit -m "auto sync" && git push


echo "Commiting woof-model"
cd ../woof-model
git add . && git commit -m "auto sync" && git push


echo "Commiting woof-view"
cd ../woof-view
git add . && git commit -m "auto sync" && git push


echo "Commiting woof"
cd ../..
git add . && git commit -m "auto sync" && git push



