<?php

namespace Deployer;

require 'recipe/symfony.php';

// Config

set('repository', 'git@github.com:re-connect/mpb.git');
set('branch', 'main');

add('shared_files', [
    '.env',
    '.env.local',
    'config/secrets/prod/prod.decrypt.private.php',
]);
add('shared_dirs', [
    'var/log',
    'var/oauth',
    'node_modules',
    'vendor',
    'public/uploads',
]);

add('writable_dirs', []);

// Hosts

host('155.133.130.39')
    ->set('remote_user', 'www-data')
    ->set('deploy_path', '~/mpb');

// Tasks

task('deploy:build_frontend', function () {
    run('cd {{release_path}} && npm run build');
});

task('deploy:install_frontend', function () {
    run('cd {{release_path}} && npm install');
});
task('deploy:assets_install', function () {
    run('cd {{release_path}} && php bin/console ckeditor:install --clear=drop --tag=4.22.1 && php bin/console assets:install public');
});
// Hooks

before('deploy:install_frontend', 'deploy:assets_install');
before('deploy:build_frontend', 'deploy:install_frontend');
before('deploy:cache:clear', 'deploy:build_frontend');
before('deploy:symlink', 'database:migrate');

after('deploy:failed', 'deploy:unlock');
