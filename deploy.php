<?php
namespace Deployer;

require 'recipe/symfony.php';

// Config

set('repository', 'git@github.com:re-connect/mpb.git');

add('shared_files', [
    '.env',
]);
add('shared_dirs', [
    'var',
    'config/secrets/prod',
    'node_modules',
    'vendor',
]);

add('writable_dirs', []);

// Hosts

host('155.133.130.39')
    ->set('remote_user', 'www-data')
    ->set('deploy_path', '~/mpb-test/www');

// Tasks

task('deploy:build_frontend', function () {
    run('npm install && npm run build');
});

// Hooks

before('deploy:cache:clear', 'deploy:build_frontend');
before('deploy:symlink', 'database:migrate');

after('deploy:failed', 'deploy:unlock');
