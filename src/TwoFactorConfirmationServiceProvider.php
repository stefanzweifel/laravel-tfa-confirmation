<?php

namespace Wnx\TfaConfirmation;

use Illuminate\Support\Facades\Event;
use Laravel\Fortify\Events\ValidTwoFactorAuthenticationCodeProvided;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Wnx\TfaConfirmation\Listeners\StoreTwoFactorConfirmedAtInSessionListener;

class TwoFactorConfirmationServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('tfa-sudo-mode')
            ->hasConfigFile()
            ->hasViews()
            ->hasRoute('web')
            ->hasTranslations()
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    // ->copyAndRegisterServiceProviderInApp()
                    ->askToStarRepoOnGitHub('wnx/tfa-sudo-mode');
            });
    }

    public function bootingPackage()
    {
        Event::listen(
            ValidTwoFactorAuthenticationCodeProvided::class,
            [StoreTwoFactorConfirmedAtInSessionListener::class, 'handle']
        );
    }
}
