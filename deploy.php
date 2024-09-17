<?php

namespace Deployer;

require 'recipe/symfony.php';

// Config

set('repository', 'git@github.com:re-connect/mpb.git');
set('remote_user', 'www-data');
set('flush_cache_file_name', 'flush-cache.php');
set('flush_cache_file_path', '{{current_path}}/public/{{flush_cache_file_name}}');
set('env', [
    'SYMFONY_ENV' => get('symfony_env'),
]);

add('shared_files', [
    '.env',
    '.env.local',
    'config/secrets/prod/prod.decrypt.private.php',
    'config/secrets/prod/preprod.decrypt.private.php',
]);
add('shared_dirs', [
    'var/log',
    'var/oauth',
    'node_modules/',
    'vendor/',
    'public/uploads',
]);

add('writable_dirs', []);

// Hosts
host('preprod')
    ->setHostname('155.133.130.39')
    ->setLabels(['stage' => 'preprod'])
    ->set('branch', 'dev')
    ->set('symfony_env', 'preprod')
    ->set('homepage_url', 'https://preprod.mpb.reconnect.fr')
    ->set('deploy_path', '~/mpb_pp');

host('prod')
    ->setHostname('155.133.130.39')
    ->setLabels(['stage' => 'prod'])
    ->set('branch', 'main')
    ->set('symfony_env', 'prod')
    ->set('homepage_url', 'https://mpb.reconnect.fr')
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
task('deploy:reset-opcache', function () {
    run('sleep 5');
    run('echo "<?php opcache_reset(); ?>" >> {{flush_cache_file_path}}');
    run('sleep 5');
    run('wget "{{homepage_url}}/{{flush_cache_file_name}}" --spider --retry-connrefused -t 5');
    run('rm {{flush_cache_file_path}}');
});
// Hooks

before('deploy:build_frontend', 'deploy:install_frontend');
before('deploy:install_frontend', 'deploy:assets_install');
before('deploy:cache:clear', 'deploy:build_frontend');
before('deploy:symlink', 'deploy:reset-opcache');
after('deploy:reset-opcache', 'database:migrate');

after('deploy:failed', 'deploy:unlock');
