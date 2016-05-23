#!/usr/bin/env bash
echo "Installing global packages"

npm install -g eslint eslint-plugin-react eslint-plugin-babel babel-eslint gulp

echo "Installing hooks"
bash $PWD/bin/install-git-hooks.sh

echo "Installing node modules"

npm install
