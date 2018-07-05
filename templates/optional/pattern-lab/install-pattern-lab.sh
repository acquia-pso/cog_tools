#!/usr/bin/env bash

# Install Pattern Lab

# Remove existing Pattern Lab directory
rm -rf pattern-lab

# Install Pattern Lab
composer create-project -n pattern-lab/edition-twig-standard pattern-lab

# Delete the default source directory
rm -rf pattern-lab/source

# Symlink our patterns directory to the source location we just deleted
ln -s ../patterns pattern-lab/source

# Credits:
# https://github.com/fourkitchens/emulsify/blob/develop/scripts/pattern_lab.sh
