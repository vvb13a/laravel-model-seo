<?php

namespace Vvb13a\LaravelModelSeo\Config;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Spatie\SchemaOrg\BaseType;
use Vvb13a\LaravelModelSeo\Config\Traits\ManagesCoreSeoFallbacks;
use Vvb13a\LaravelModelSeo\Config\Traits\ManagesOpenGraphFallbacks;
use Vvb13a\LaravelModelSeo\Config\Traits\ManagesSchemaOrgResolvers;
use Vvb13a\LaravelModelSeo\Config\Traits\ManagesTwitterCardFallbacks;
use Vvb13a\LaravelModelSeo\Services\SeoHandler;

class SeoConfig
{
    use ManagesCoreSeoFallbacks;
    use ManagesOpenGraphFallbacks;
    use ManagesTwitterCardFallbacks;
    use ManagesSchemaOrgResolvers;

    public static function make(): self
    {
        return new static();
    }

    /**
     * Get a resolved value for a specific SEO attribute.
     *
     * @param  string  $property  The name of the property (e.g., 'titleFallback').
     * @param  Model  $model  The current model instance.
     * @param  mixed  ...$args  Additional arguments to pass ONLY IF the property holds a Closure.
     * @return mixed|null The resolved value.
     */
    public function resolve(string $property, Model $model, ...$args)
    {
        // This check is important because traits add properties dynamically
        if (!property_exists($this, $property) || $this->{$property} === null) {
            return null;
        }

        $value = $this->{$property};

        if ($value instanceof Closure) {
            return call_user_func_array($value, array_merge([$model], $args));
        }

        return $value;
    }

    public function resolveSchemaType(Model $model): ?BaseType
    {
        if ($this->schemaTypeResolver === null) {
            return null;
        }

        $resolver = $this->schemaTypeResolver;

        if ($resolver instanceof Closure) {
            $resolved = call_user_func($resolver, $model);
            return ($resolved instanceof BaseType) ? $resolved : null;
        }

        if ($resolver instanceof BaseType) {
            return $resolver;
        }
        return null;
    }

    public function resolveSchemaProperties(Model $model, SeoHandler $seoHandler): ?array
    {
        if ($this->schemaPropertiesResolver instanceof Closure) {
            $resolved = call_user_func_array($this->schemaPropertiesResolver, [$model, $seoHandler]);
            return is_array($resolved) ? $resolved : null;
        }
        return null;
    }
}
