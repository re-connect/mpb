<?php

use EasyCorp\Bundle\EasyDeployBundle\Configuration\DefaultConfiguration;
use EasyCorp\Bundle\EasyDeployBundle\Deployer\DefaultDeployer;

return new class() extends DefaultDeployer {
    public function configure(): DefaultConfiguration
    {
        return $this->getConfigBuilder()
            ->server('www-data@155.133.130.39')
            ->deployDir('/var/www/mpb/www')
            ->repositoryUrl('git@github.com:re-connect/mpb.git')
            ->repositoryBranch('master')
            ->remoteComposerBinaryPath('/var/www/mpb/composer.phar')
            ->useSshAgentForwarding(true)
            ->composerInstallFlags('--prefer-dist --no-interaction')
            ->sharedFilesAndDirs(
                [
                    '.env',
                    'var/log',
                    'var/oauth',
                    'config/secrets/prod/prod.decrypt.private.php',
                    'vendor/',
                    'node_modules/',
                ]
            );
    }

    public function beforePublishing(): void
    {
        $this->runRemote('npm install');
        $this->runRemote('npm run build');
    }

    public function beforeFinishingDeploy(): void
    {
//        $this->runRemote('php bin/console ckeditor:install && php bin/console assets:install public');
        $this->runRemote('{{ console_bin }} doctrine:migrations:migrate -q');
    }
};
