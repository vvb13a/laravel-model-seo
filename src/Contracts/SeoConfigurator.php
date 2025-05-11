<?php

namespace Vvb13a\LaravelModelSeo\Contracts;

use Vvb13a\LaravelModelSeo\Services\Seo;

interface SeoConfigurator
{
    /**
     * Static method for configuring the Seo instance for this model.
     * Models should override this method to apply configurations fluently.
     * If not overridden, the Seo Service will apply its defaults internally.
     *
     * @param  Seo  $seo  The seo instance to configure.
     * @return void
     */
    public static function configureSeo(Seo $seo): void;
}
