<?php

namespace Vvb13a\LaravelModelSeo;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelModelSeoServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-model-seo')
            ->hasViews()
            ->hasMigration('create_seo_data_table');
    }
}
