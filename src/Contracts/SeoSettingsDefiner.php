<?php

namespace Vvb13a\LaravelModelSeo\Contracts;

use Vvb13a\LaravelModelSeo\Config\SeoConfig;

interface SeoSettingsDefiner
{
    /**
     * Define and return the SEO configuration for a model.
     */
    public function define(): SeoConfig;
}
