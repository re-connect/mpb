<?php

use EasyCorp\Bundle\EasyDeployBundle\Deployer\DefaultDeployer;

return new class extends DefaultDeployer {
    public function configure()
    {
        return $this->getConfigBuilder()
            // SSH connection string to connect to the remote server (format: user@host-or-IP:port-number)
            ->server('www-data@155.133.130.39')
            // the absolute path of the remote server directory where the project is deployed
            ->deployDir('/var/www/mpb/www')
            // the URL of the Git repository where the project code is hosted
            ->repositoryUrl('git@github.com:re-connect/mpb.git')
            // the repository branch to deploy
            ->repositoryBranch('master')
            ->remoteComposerBinaryPath('/var/www/mpb/composer.phar')
            ->useSshAgentForwarding(false)
            ->composerInstallFlags('--prefer-dist --no-interaction')
            ->sharedFilesAndDirs(
                [
                    '.env',
                    'var/log',
                    'var/oauth',
                    'vendor/',
                    'node_modules/',
                ]
            );
    }

    public function beforePreparing()
    {
        $this->log('Remote yarn');
        $this->runRemote('~/.yarn/bin/yarn install');
        $this->runRemote('~/.yarn/bin/yarn build');
    }

    public function beforeFinishingDeploy()
    {
        $this->runRemote('{{ console_bin }} doctrine:migrations:migrate -q');
    }
};
