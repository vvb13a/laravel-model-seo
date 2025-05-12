<?php

namespace Vvb13a\LaravelModelSeo\Concerns;

use Illuminate\Database\Eloquent\Relations\MorphOne;
use Vvb13a\LaravelModelSeo\Contracts\SeoConfigurator;
use Vvb13a\LaravelModelSeo\Exceptions\ConfigurationErrorException;
use Vvb13a\LaravelModelSeo\Models\SeoData;
use Vvb13a\LaravelModelSeo\Services\Seo;

trait HasSeo
{
    protected ?Seo $seoInstance = null;

    //    public static function bootHasSeo(): void
    //    {
    //        static::deleted(function (Model $model) {
    //            if (method_exists($model, 'isForceDeleting') && !$model->isForceDeleting()) {
    //                return;
    //            }
    //            $model->seoData()->delete();
    //        });
    //    }

    public function getSeoTestAttribute(): Seo
    {
        if ($this->seoInstance === null) {
            $seo = Seo::make($this);
            $this->applySeoConfiguration($seo);
            $this->seoInstance = $seo;
        }

        return $this->seoInstance;
    }

    public function applySeoConfiguration(Seo $seo): void
    {
        if (property_exists(static::class, 'seoConfigurator') && static::$seoConfigurator !== null) {
            $className = static::$seoConfigurator;
            $modelClass = static::class;

            if (! class_exists($className)) {
                throw ConfigurationErrorException::seoSettingsClassNotFound($modelClass, $className);
            }

            if (! is_subclass_of($className, SeoConfigurator::class)) {
                throw ConfigurationErrorException::seoSettingsClassInvalidInterface(
                    $modelClass,
                    $className,
                    SeoConfigurator::class
                );
            }

            $className::configureSeo($seo);
        } else {
            static::configureSeo($seo);
        }
    }

    /**
     * Static method for configuring the Seo instance for this model.
     * Models should override this method to apply configurations fluently.
     * If not overridden, the Seo Service will apply its defaults internally.
     *
     * @param  Seo  $seo  The seo instance to configure.
     */
    public static function configureSeo(Seo $seo): void
    {
        // configure
    }

    public function updateSeo(array $data): SeoData
    {
        $seoData = $this->getOrCreateSeoData();
        $seoData->fill($data);
        $seoData->save();

        $this->load('seoData');
        $this->seoInstance = null;

        return $seoData;
    }

    public function getOrCreateSeoData(array $attributes = []): SeoData
    {
        $seoData = $this->seoData()->firstOrNew([]);

        if (! empty($attributes)) {
            $seoData->fill($attributes);
        }

        return $seoData;
    }

    public function seoData(): MorphOne
    {
        return $this->morphOne(SeoData::class, 'seoable');
    }
}
