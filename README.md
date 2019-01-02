# Cog Tools

A Drupal 8 sub theme generator, with build tools.

Enables sub theme generation via drush 9.

## Quickstart:

As you should only need to run these commands once. We recommend downloading this repository once and then deleting this repo before committing it to your local repository.
 
Download the cog_tools module in your custom/modules directory.

`cd docroot/modules/custom`

`git@github.com:acquia-pso/cog_tools.git`
 
Enable the cog_tools module

`drush pm:enable cog_tools`

Create a sub theme with drush.

`drush generate cog`

Answer the questions.

Enable your new sub theme. For a theme with the machine name `durian`:

`drush theme:enable durian`

Remove the module from your repository after development tasks are done.

`rm -rf docroot/modules/custom/cog_tools`


## Advanced topics

Passing in arguments via the command line:

`drush gen cog --answers '{"name":"Durian", "machine_name": "durian", "base_theme": "classy", "description": "What a nice theme.", "package": "Custom", "build_tasks": "yes", "layouts":"yes", "theme_settings":"yes","style_guide":"yes"}'`

Any answers that are left off here will be asked still, so this could be handy if you have a few options you almost always select.
