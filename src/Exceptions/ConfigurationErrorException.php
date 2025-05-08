<?php

namespace Vvb13a\LaravelModelSeo\Exceptions;

use Exception;

class ConfigurationErrorException extends Exception
{
    public static function invalidSeoConfigReturnType(string $modelClass, string $methodName): self
    {
        return new static("The method {$modelClass}::{$methodName}() must return an instance of YourVendor\\YourPackageName\\Config\\SeoConfig.");
    }

    public static function seoSettingsClassNotFound(string $modelClass, string $settingsClassName): self
    {
        return new static("The SEO settings class '{$settingsClassName}' specified in {$modelClass}::\$seoSettingsClass was not found.");
    }

    public static function seoSettingsClassInvalidInterface(
        string $modelClass,
        string $settingsClassName,
        string $expectedInterface
    ): self {
        return new static("The SEO settings class '{$settingsClassName}' (from {$modelClass}::\$seoSettingsClass) must implement the {$expectedInterface} interface.");
    }

    public static function seoSettingsClassDefineMethodInvalidReturn(
        string $settingsClassName,
        string $modelClass
    ): self {
        return new static("The method {$settingsClassName}::define() (from {$modelClass}::\$seoSettingsClass) must return an instance of YourVendor\\YourPackageName\\Config\\SeoConfig.");
    }
}
