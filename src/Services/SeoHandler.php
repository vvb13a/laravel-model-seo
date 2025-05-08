<?php

namespace Vvb13a\LaravelModelSeo\Services;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\View;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Spatie\SchemaOrg\BaseType as SchemaOrgBaseType;
use Spatie\SchemaOrg\Schema as SchemaOrgBuilder;
use Spatie\SchemaOrg\WebPage;
use Vvb13a\LaravelModelSeo\Config\DefaultSeoConfigBuilder;
use Vvb13a\LaravelModelSeo\Config\SeoConfig;
use Vvb13a\LaravelModelSeo\Exceptions\ConfigurationErrorException;
use Vvb13a\LaravelModelSeo\Models\SeoData;

class SeoHandler
{
    protected Model $model;

    protected ?SeoData $seoDataRecord;

    protected SeoConfig $modelSeoConfig;

    protected array $globalPackageConfig;

    public function __construct(Model $model, ?SeoData $seoDataRecord)
    {
        $this->model = $model;
        $this->seoDataRecord = $seoDataRecord;
        $this->globalPackageConfig = config('model-seo', []);
        $this->modelSeoConfig = $this->resolveModelSeoConfig($model);
    }

    /**
     * Resolves the SeoConfig instance for the given model.
     * It prioritizes the model's specific configuration, falling back to a global default.
     *
     * @throws ConfigurationErrorException If model's getSeoConfiguration returns an invalid type.
     */
    protected function resolveModelSeoConfig(Model $model): SeoConfig
    {
        $config = $model::getSeoConfiguration();

        if ($config instanceof SeoConfig) {
            return $config;
        }

        if ($config === null) {
            return app(DefaultSeoConfigBuilder::class)->build();
        }

        throw ConfigurationErrorException::invalidSeoConfigReturnType(
            get_class($model),
            'getSeoConfiguration'
        );
    }

    public function getKeywords(): ?string
    {
        $keywords = $this->seoDataRecord?->keywords
            ?? $this->modelSeoConfig->resolve('keywordsFallback', $this->model);

        return $keywords ? Str::limit($keywords, 255) : null;
    }

    public function getRobots(): string
    {
        return $this->seoDataRecord?->robots
            ?? $this->modelSeoConfig->resolve('robotsDirectiveFallback', $this->model)
            ?? $this->globalPackageConfig['defaults']['robots'] ?? 'index,follow';
    }

    public function getOgType(): string
    {
        return $this->modelSeoConfig->resolve('ogTypeFallback', $this->model)
            ?? $this->globalPackageConfig['defaults']['og_type'] ?? 'website';
    }

    public function getOgSiteName(): ?string
    {
        return $this->globalPackageConfig['defaults']['og_site_name'] ?? $this->getDefaultSiteName();
    }

    protected function getDefaultSiteName(): string
    {
        return $this->globalPackageConfig['site_name'] ?? config('app.name', 'Laravel');
    }

    public function getOgLocale(): ?string
    {
        return $this->globalPackageConfig['defaults']['og_locale'] ?? str_replace('_', '-', app()->getLocale());
    }

    public function getTwitterCard(): string
    {
        return $this->modelSeoConfig->resolve('twitterCardTypeFallback', $this->model)
            ?? $this->globalPackageConfig['defaults']['twitter_card'] ?? 'summary_large_image';
    }

    public function getTwitterTitle(): string
    {
        return $this->modelSeoConfig->resolve('twitterTitleFallback', $this->model)
            ?? $this->getOgTitle();
    }

    public function getOgTitle(): string
    {
        return $this->modelSeoConfig->resolve('ogTitleFallback', $this->model)
            ?? $this->getTitle();
    }

    public function getTitle(): string
    {
        $title = $this->seoDataRecord?->title
            ?? $this->modelSeoConfig->resolve('titleFallback', $this->model)
            ?? $this->getDefaultSiteName();

        return $this->formatTitle($title);
    }

    protected function formatTitle(?string $title): string
    {
        $title = trim($title ?? '');
        $siteName = $this->getDefaultSiteName();
        $separator = ' '.trim($this->globalPackageConfig['title_separator'] ?? '|').' ';

        if (empty($title)) {
            return $siteName;
        }

        if ($this->globalPackageConfig['title_append_site_name'] ?? true) {
            $title = $title.$separator.$siteName;
        } elseif (($this->globalPackageConfig['title_prepend_site_name'] ?? false)) {
            $title = $siteName.$separator.$title;
        }

        $maxLength = $this->globalPackageConfig['max_title_length'] ?? 60;

        return Str::limit($title, $maxLength, '');
    }

    public function getTwitterDescription(): string
    {
        return $this->modelSeoConfig->resolve('twitterDescriptionFallback', $this->model)
            ?? $this->getOgDescription();
    }

    public function getOgDescription(): string
    {
        return $this->modelSeoConfig->resolve('ogDescriptionFallback', $this->model)
            ?? $this->getDescription();
    }

    public function getDescription(): string
    {
        $description = $this->seoDataRecord?->description
            ?? $this->modelSeoConfig->resolve('descriptionFallback', $this->model);

        return $this->formatDescription($description);
    }

    protected function formatDescription(?string $description): string
    {
        $description = trim(strip_tags($description ?? ''));
        if (empty($description)) {
            return '';
        }

        $maxLength = $this->globalPackageConfig['max_description_length'] ?? 160;

        return Str::limit($description, $maxLength);
    }

    public function getTwitterImageUrl(): ?string
    {
        return $this->modelSeoConfig->resolve('twitterImageUrlFallback', $this->model)
            ?? $this->getOgImageUrl();
    }

    public function getOgImageUrl(): ?string
    {
        return $this->modelSeoConfig->resolve('ogImageUrlFallback', $this->model)
            ?? $this->globalPackageConfig['defaults']['og_image'] ?? null;
    }

    public function getTwitterSite(): ?string
    {
        return $this->modelSeoConfig->resolve('twitterSiteHandleFallback', $this->model)
            ?? $this->globalPackageConfig['defaults']['twitter_site'] ?? null;
    }

    public function getTwitterCreator(): ?string
    {
        return $this->modelSeoConfig->resolve('twitterCreatorHandleFallback', $this->model);
    }

    public function getJsonLdScript(): ?HtmlString
    {
        if (! class_exists(SchemaOrgBuilder::class)) {
            return null;
        }

        try {
            /** @var SchemaOrgBaseType|null $schemaTypeInstance */
            $schemaTypeInstance = $this->modelSeoConfig->resolveSchemaType($this->model);

            if (! $schemaTypeInstance) {
                $defaultSchemaClass = $this->globalPackageConfig['schema_org']['default_type'] ?? WebPage::class;
                if (class_exists($defaultSchemaClass) && is_subclass_of($defaultSchemaClass,
                    SchemaOrgBaseType::class)) {
                    $schemaTypeInstance = new $defaultSchemaClass;
                } else {
                    return null;
                }
            }

            $this->autoFillSchemaProperties($schemaTypeInstance);

            $customProperties = $this->modelSeoConfig->resolveSchemaProperties($this->model, $this) ?? [];
            foreach ($customProperties as $method => $value) {
                if (method_exists($schemaTypeInstance, $method)) {
                    // Resolve callable values (though less common here as properties usually defined in closure)
                    $resolvedValue = is_callable($value) ? call_user_func($value, $this) : $value;
                    if ($resolvedValue !== null) {
                        $schemaTypeInstance->{$method}($resolvedValue);
                    }
                }
            }

            return $schemaTypeInstance->toScript();

        } catch (Exception $e) {
            report($e);

            return null;
        }
    }

    /**
     * Auto-fills common schema properties if not already set.
     * This is called before model-specific schema properties are applied.
     */
    protected function autoFillSchemaProperties(SchemaOrgBaseType &$schema): void
    {
        // URL
        if (method_exists($schema, 'url') && empty($schema->getProperty('url'))) {
            if ($url = $this->getOgUrl()) {
                $schema->url($url);
            }
        }
        $ogTitle = $this->getOgTitle();
        if (method_exists($schema, 'name') && empty($schema->getProperty('name'))) {
            $schema->name($ogTitle);
        } elseif (method_exists($schema, 'headline') && empty($schema->getProperty('headline'))) {
            $schema->headline($ogTitle);
        }
        // Description
        if (method_exists($schema, 'description') && empty($schema->getProperty('description'))) {
            $schema->description($this->getOgDescription());
        }
        // Image
        if (method_exists($schema, 'image') && empty($schema->getProperty('image'))) {
            if ($image = $this->getOgImageUrl()) {
                $schema->image($image);
            }
        }
        // Date Published & Modified (if model has timestamps and schema type supports it)
        if ($this->model->usesTimestamps()) {
            if (method_exists($schema,
                'datePublished') && empty($schema->getProperty('datePublished')) && $this->model->created_at) {
                $schema->datePublished($this->model->created_at);
            }
            if (method_exists($schema,
                'dateModified') && empty($schema->getProperty('dateModified')) && $this->model->updated_at) {
                $schema->dateModified($this->model->updated_at);
            }
        }
    }

    public function getOgUrl(): ?string
    {
        return $this->getCanonicalUrl() ?? request()->url();
    }

    public function getCanonicalUrl(): ?string
    {
        $url = $this->seoDataRecord?->canonical_url
            ?? $this->modelSeoConfig->resolve('canonicalUrlFallback', $this->model);

        if ($url) {
            return $url;
        }

        if (request()->route() && $this->isModelOnCurrentRoute()) {
            return request()->url();
        }

        return null;
    }

    /**
     * Checks if the current model instance is likely the primary subject of the current route.
     * This is a heuristic and might need refinement for complex routing scenarios.
     */
    protected function isModelOnCurrentRoute(): bool
    {
        $route = request()->route();
        if (! $route) {
            return false;
        }

        $routeParameters = $route->parameters();
        // Check if any route parameter is an instance of our model and matches it
        foreach ($routeParameters as $param) {
            if ($param instanceof Model && $param->is($this->model)) {
                return true;
            }
        }
        // Check if the model's route key is present in parameters and matches
        $routeKeyName = $this->model->getRouteKeyName();
        if (isset($routeParameters[$routeKeyName]) && $routeParameters[$routeKeyName] == $this->model->{$routeKeyName}) {
            return true;
        }
        // Check for conventional snake_case model name as route parameter
        $conventionalParamName = Str::snake(class_basename($this->model));
        if (isset($routeParameters[$conventionalParamName]) && $routeParameters[$conventionalParamName] == $this->model->{$routeKeyName}) {
            return true;
        }

        return false;
    }

    public function renderMetaTags(): HtmlString
    {
        $viewName = $this->globalPackageConfig['views']['meta_tags'] ?? 'model-seo::meta_tags';

        return new HtmlString(
            View::make($viewName)
                ->with('seo', $this)
                ->render()
        );
    }
}
