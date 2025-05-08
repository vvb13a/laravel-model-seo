<?php

namespace Vvb13a\LaravelModelSeo\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Vvb13a\LaravelModelSeo\Config\SeoConfig;
use Vvb13a\LaravelModelSeo\Contracts\SeoSettingsDefiner;
use Vvb13a\LaravelModelSeo\Exceptions\ConfigurationErrorException;
use Vvb13a\LaravelModelSeo\Models\SeoData;
use Vvb13a\LaravelModelSeo\Services\SeoHandler;

trait HasSeo
{
    protected ?SeoHandler $seoHandlerInstance = null;

    public static function bootHasSeo(): void
    {
        static::deleted(function (Model $model) {
            if (method_exists($model, 'isForceDeleting') && ! $model->isForceDeleting()) {
                return;
            }
            $model->seoDataRelation()->delete();
        });
    }

    public function seoDataRelation(): MorphOne
    {
        return $this->morphOne(SeoData::class, 'seoable');
    }

    /**
     * Get the SEO configuration definition for this model.
     *
     * Models can override this method to define their SeoConfig fluently inline.
     * By default, this method looks for a static '$seoSettingsClass' property
     * on the model, which should point to a class implementing SeoSettingsDefiner.
     *
     * @return SeoConfig|null Returns a SeoConfig instance or null if no specific config is found,
     *                        prompting the SeoHandler to use its global defaults.
     */
    public static function getSeoConfiguration(): ?SeoConfig // Renamed for consistency
    {
        if (property_exists(static::class, 'seoSettingsClass') && static::$seoSettingsClass !== null) {
            $className = static::$seoSettingsClass;
            $modelClass = static::class;

            if (! class_exists($className)) {
                throw ConfigurationErrorException::seoSettingsClassNotFound($modelClass, $className);
            }

            if (! is_subclass_of($className, SeoSettingsDefiner::class)) {
                throw ConfigurationErrorException::seoSettingsClassInvalidInterface(
                    $modelClass,
                    $className,
                    SeoSettingsDefiner::class
                );
            }

            $settingsInstance = app($className);
            $config = $settingsInstance->define();

            if (! $config instanceof SeoConfig) {
                throw ConfigurationErrorException::seoSettingsClassDefineMethodInvalidReturn(
                    $className,
                    $modelClass
                );
            }

            return $config;
        }

        return null;
    }

    public function getSeoAttribute(): SeoHandler
    {
        if (! isset($this->seoHandlerInstance)) {
            $this->seoHandlerInstance = app(SeoHandler::class, [
                'model' => $this,
                'seoData' => $this->seoDataRelation,
            ]);
        }

        return $this->seoHandlerInstance;
    }

    public function updateSeo(array $data): SeoData
    {
        $seoData = $this->getOrCreateSeoData();
        $seoData->fill($data);
        $seoData->save();

        $this->load('seoDataRelation');
        unset($this->seoHandlerInstance);

        return $seoData;
    }

    public function getOrCreateSeoData(array $attributes = []): SeoData
    {
        $seoData = $this->seoDataRelation()->firstOrNew([]);

        if (! empty($attributes)) {
            $seoData->fill($attributes);
        }

        return $seoData;
    }
}
