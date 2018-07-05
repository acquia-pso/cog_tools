<?php

namespace Drupal\cog_tools\Generators;

use DrupalCodeGenerator\Command\BaseGenerator;
use DrupalCodeGenerator\Utils;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;


/**
 * Drush theme generator.
 */
class CogThemeGenerator extends BaseGenerator {
  protected $name = 'cog-theme';
  protected $description = 'Generates a cog theme.';
  protected $alias = 'cog';
  protected $templatePath = __DIR__ . "/../../templates";
  protected $destination = 'themes';

  /**
   * Prompt the user for desired theme options.
   */
  protected function interact(InputInterface $input, OutputInterface $output) {
    $questions['name'] = new Question('Theme name');
    $questions['machine_name'] = new Question('Theme machine name');
    $questions['base_theme'] = new Question('Base theme', 'classy');
    $questions['description'] = new Question('Description', 'Acquia D8 starter theme');
    $questions['package'] = new Question('Package', 'Custom');

    $vars = $this->collectVars($input, $output, $questions);

    // Additional files.
    $option_questions['build_tasks'] = new ChoiceQuestion('Would you like to add build tasks?', ['no', 'gulp'], 'gulp');
    $option_questions['layouts'] = new ConfirmationQuestion('Would you like to add layout files?', FALSE);
    $option_questions['theme_settings'] = new ConfirmationQuestion('Would you like to add starter theme settings files?', FALSE);
    $option_questions['style_guide'] = new ChoiceQuestion('Would you like to include the style guide? (KSS = "KSS Node", PL = "Pattern Lab"', ['No', 'KSS', 'PL'], 'No');

    $options = $this->collectVars($input, $output, $option_questions);

    $output->writeln('Creating sub theme!');

    $vars['base_theme'] = Utils::human2machine($vars['base_theme']);

    // Where (inside themes/) to put this stuff, hardcoded for now.
    $location = 'custom/';

    $prefix = $vars['machine_name'] . '/' . $vars['machine_name'];

    // Core cog stuff.
    $this->addFile()
      ->path($location . '{machine_name}/README.md')
      ->template('starterkit/README.md');

    $this->addFile()
      ->path($location . $prefix . '.info.yml')
      ->template('starterkit/theme-info.twig');

    $this->addFile()
      ->path($location . '{machine_name}/logo.svg')
      ->template('starterkit/theme-logo.twig');

    $this->addFile()
      ->path($location . $prefix . '.libraries.yml')
      ->template('starterkit/theme-libraries.twig');

    $this->addFile()
      ->path($location . $prefix . '.breakpoints.yml')
      ->template('starterkit/breakpoints.twig');

    $this->addFile()
      ->path($location . $prefix . '.theme')
      ->template('starterkit/theme.twig');

    // Front end tools.
    $this->addFile()
      ->path($location . '{machine_name}/package.json')
      ->template('starterkit/package.twig');

    $this->addFile()
      ->path($location . '{machine_name}/.eslintignore')
      ->template('starterkit/.eslintignore');

    $this->addFile()
      ->path($location . '{machine_name}/.stylelintrc.json')
      ->template('starterkit/.stylelintrc.json');

    // Example and starter files.
    if ($options['theme_settings']) {
      $this->addFile()
        ->path($location . '{machine_name}/theme-settings.php')
        ->template('starterkit/theme-settings-form.twig');

      $this->addFile()
        ->path($location . '{machine_name}/config/install/{machine_name}.settings.yml')
        ->template('starterkit/theme-settings-config.twig');

      $this->addFile()
        ->path($location . '{machine_name}/config/schema/{machine_name}.schema.yml')
        ->template('starterkit/theme-settings-schema.twig');
    }

    // Empty directories.
    $this->addDirectory()
      ->path($location . '{machine_name}/templates');

    $this->addDirectory()
      ->path($location . '{machine_name}/images');

    // Node and nvm install script.
    $this->addFile()
      ->path($location . '{machine_name}/scripts/install-node.sh')
      ->template('starterkit/install-node.sh');

    // SCSS files.
    // If KSS is selected, these include KSS comments, otherwise just styling.
    $scss_files = [
      '_config.scss',
      '_utilities.scss',
      'base/elements.scss',
      'components/branding/branding.scss',
      'components/breadcrumb/breadcrumb.scss',
      'components/form/form.scss',
      'components/header/header.scss',
      'components/menu/menu.scss',
      'components/messages/messages.scss',
      'components/tabs/tabs.scss',
      'components/buttons/buttons.scss',
      'layouts/layout-main.scss',
      'theme/print.scss',
    ];
    foreach ($scss_files as $file) {
      $this->addFile()
        ->path($location . '{machine_name}/patterns/' . $file)
        ->template('optional/patterns/' . $file . '.twig');
    }

    // Gulp build tasks.
    if ($options['build_tasks'] == 'gulp') {
      $output->writeln('Adding gulp tasks.');

      $this->addFile()
        ->path($location . '{machine_name}/gulpfile.js')
        ->template('optional/gulpfile.twig');

      $this->addFile()
        ->path($location . '{machine_name}/config.json')
        ->template('optional/config.json');

      $this->addFile()
        ->path($location . '{machine_name}/gulp-tasks/browser-sync.js')
        ->template('optional/gulp-tasks/browser-sync.js');
      $this->addFile()
        ->path($location . '{machine_name}/gulp-tasks/build.js')
        ->template('optional/gulp-tasks/build.js');
      $this->addFile()
        ->path($location . '{machine_name}/gulp-tasks/clean.js')
        ->template('optional/gulp-tasks/clean.js');
      $this->addFile()
        ->path($location . '{machine_name}/gulp-tasks/clean-css.js')
        ->template('optional/gulp-tasks/clean-css.js');

      $this->addFile()
        ->path($location . '{machine_name}/gulp-tasks/clean-styleguide.js')
        ->template('optional/gulp-tasks/clean-styleguide.js');
      $this->addFile()
        ->path($location . '{machine_name}/gulp-tasks/compile-styleguide.js')
        ->template('optional/gulp-tasks/compile-styleguide.js');

      $this->addFile()
        ->path($location . '{machine_name}/gulp-tasks/pattern-lab.js')
        ->template('optional/gulp-tasks/pattern-lab.js');

      $this->addFile()
        ->path($location . '{machine_name}/gulp-tasks/compile-js.js')
        ->template('optional/gulp-tasks/compile-js.js');
      $this->addFile()
        ->path($location . '{machine_name}/gulp-tasks/compile-sass.js')
        ->template('optional/gulp-tasks/compile-sass.js');

      $this->addFile()
        ->path($location . '{machine_name}/gulp-tasks/default.js')
        ->template('optional/gulp-tasks/default.js');
      $this->addFile()
        ->path($location . '{machine_name}/gulp-tasks/lint-css.js')
        ->template('optional/gulp-tasks/lint-css.js');
      $this->addFile()
        ->path($location . '{machine_name}/gulp-tasks/lint-js.js')
        ->template('optional/gulp-tasks/lint-js.js');
      $this->addFile()
        ->path($location . '{machine_name}/gulp-tasks/minify-css.js')
        ->template('optional/gulp-tasks/minify-css.js');
      $this->addFile()
        ->path($location . '{machine_name}/gulp-tasks/pa11y.js')
        ->template('optional/gulp-tasks/pa11y.js');
      $this->addFile()
        ->path($location . '{machine_name}/gulp-tasks/serve.js')
        ->template('optional/gulp-tasks/serve.js');
      $this->addFile()
        ->path($location . '{machine_name}/gulp-tasks/test-css.js')
        ->template('optional/gulp-tasks/test-css.js');
      $this->addFile()
        ->path($location . '{machine_name}/gulp-tasks/watch.js')
        ->template('optional/gulp-tasks/watch.js');
    }

    // Layouts.
    if ($options['layouts']) {
      $output->writeln('Adding layout files.');

      $this->addFile()
        ->path($location . $prefix . '.layouts.yml')
        ->template('optional/layouts.twig');

      $this->addFile()
        ->path($location . '{machine_name}/patterns/layouts/layouts.scss')
        ->template('optional/patterns/layouts/layouts.scss');

      $dir = $this->templatePath . '/optional/layouts';
      $directories = array_diff(scandir($dir), ['..', '.']);

      foreach ($directories as $directory) {
        $dir = $this->templatePath . '/optional/layouts/' . $directory;
        $files = array_diff(scandir($dir), ['..', '.']);
        foreach ($files as $file) {
          $this->addFile()
            ->path($location . '{machine_name}/layouts/' . $directory . '/' . $file)
            ->template('optional/layouts/' . $directory . '/' . $file);
        }
      }
    }

    if ($options['style_guide'] == 'KSS') {
      $output->writeln('Do KSS Node style guide!');
      // Pattern twig files.
      // Only copied if KSS is selected.
      $twig_files = [
        'base/blockquote.twig',
        'base/headlines.twig',
        'base/lists.twig',
        'base/table.twig',
        'base/text.twig',
        'components/branding/branding.twig',
        'components/breadcrumb/breadcrumb.twig',
        'components/form/form_html.twig',
        'components/form/form_html5.twig',
        'components/header/header.twig',
        'components/menu/menu.twig',
        'components/messages/message.twig',
        'components/tabs/tabs.twig',
        'components/buttons/button.twig',
        'layouts/layout.twig',
      ];
      foreach ($twig_files as $file) {
        $this->addFile()
          ->path($location . '{machine_name}/patterns/' . $file)
          ->template('optional/patterns/' . $file);
      }

      $this->addFile()
        ->path($location . '{machine_name}/patterns/style-guide-only/homepage.md')
        ->template('optional/patterns/style-guide-only/homepage.md');

      $this->addFile()
        ->path($location . '{machine_name}/patterns/style-guide-only/kss-only.scss')
        ->template('optional/patterns/style-guide-only/kss-only.scss');
    }

    if ($options['style_guide'] == 'PL') {
      // @TODO put pattern lab options here.
      $output->writeln('Do Pattern Lab style guide!');

      $this->addFile()
        ->path($location . '{machine_name}/scripts/install-pattern-lab.sh')
        ->template('optional/pattern-lab/install-pattern-lab.sh');
    }

  }

}
