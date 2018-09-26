/**
 * @file
 * Task: Pattern Lab.
 */

/* global module */

module.exports = function (gulp, plugins, options) {
  'use strict';

  const spawn = require('child_process').spawn;

  // Serve pattern lab and autoload changes.
  gulp.task('patternlab:serve', function() {
    return spawn('cd pattern-lab && php core/console --server --with-watch', {
      stdio: 'inherit',
      shell: true
    });
  });

  // Export Pattern lab.
  gulp.task('patternlab:export', function() {
    return spawn('cd pattern-lab && composer install && pwd && php core/console --export', {
      stdio: 'inherit',
      shell: true
    });

  });

  // Generate Pattern lab.
  gulp.task('patternlab:generate', function() {
    return spawn('cd pattern-lab && php core/console --generate', {
      stdio: 'inherit',
      shell: true
    });
  });

};
