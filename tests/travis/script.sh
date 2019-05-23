#!/usr/bin/env bash

# NAME
#     script.sh - Run tests
#
# SYNOPSIS
#     script.sh
#
# DESCRIPTION
#     Runs static code analysis and automated tests.

cd "$(dirname "$0")"; source _includes.sh

orca fixture:status

drush generate cog --answers '
  {
      "name": "Durian",
      "machine_name": "durian",
      "base_theme": "classy",
      "description": "What a nice theme.",
      "package": "Custom",
      "build_tasks": true,
      "layouts": true,
      "theme_settings": true,
      "style_guide": true
  }'

drush pm:uninstall cog_tools

cd ${ORCA_FIXTURE_DIR}/docroot/themes/custom/durian

npm run install-tools

rpm run build

drush theme:enable durian

orca tests:run --sut=${ORCA_SUT_NAME} --sut-only
