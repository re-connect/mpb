<?php

namespace Deployer;

require 'recipe/symfony.php';

// Config

set('repository', 'git@github.com:re-connect/mpb.git');

add('shared_files', [
    '.env.local',
    'config/secrets/prod/prod.decrypt.private.php',
]);
add('shared_dirs', [
    'var/log',
    'var/oauth',
    'node_modules',
    'vendor',
]);

add('writable_dirs', []);

// Hosts

host('155.133.130.39')
    ->set('remote_user', 'www-data')
    ->set('deploy_path', '~/mpb');

// Tasks

task('deploy:build_frontend', function () {
    run('cd {{release_path}} && yarn install && yarn build');
});
task('deploy:assets_install', function () {
    run('cd {{release_path}} && php bin/console ckeditor:install && php bin/console assets:install public');
});
// Hooks

before('deploy:assets_install', 'deploy:build_frontend');
before('deploy:cache:clear', 'deploy:assets_install');
before('deploy:symlink', 'database:migrate');

after('deploy:failed', 'deploy:unlock');
