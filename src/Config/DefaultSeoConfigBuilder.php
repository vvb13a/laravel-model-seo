<?php

namespace Vvb13a\LaravelModelSeo\Config;

use Spatie\SchemaOrg\BaseType as SchemaOrgBaseType;
use Spatie\SchemaOrg\WebPage as DefaultSchemaOrgWebPage;

class DefaultSeoConfigBuilder
{
    protected array $globalPackageConfig;

    public function __construct()
    {
        $this->globalPackageConfig = config('model-seo', []);
    }

    public function build(): SeoConfig
    {
        $config = SeoConfig::make();
        $globalDefaults = $this->globalPackageConfig['defaults'] ?? [];
        $globalSchemaDefaults = $this->globalPackageConfig['schema_org'] ?? [];

        // --- Core Text ---
        $config->title($globalDefaults['title'] ?? null);
        $config->description($globalDefaults['description'] ?? null);
        $config->keywords($globalDefaults['keywords'] ?? null);

        // --- URL & Control ---
        $config->canonicalUrl($globalDefaults['canonical_url'] ?? null);
        $config->robots($globalDefaults['robots'] ?? 'index,follow');

        // --- Open Graph ---
        $config->ogTitle($globalDefaults['og_title'] ?? null);
        $config->ogDescription($globalDefaults['og_description'] ?? null);
        $config->ogImageUrl($globalDefaults['og_image_url'] ?? null);
        $config->ogType($globalDefaults['og_type'] ?? 'website'); // 'website' is a sensible hard default

        // --- Twitter Card ---
        $config->twitterTitle($globalDefaults['twitter_title'] ?? null); // If null, SeoHandler defaults to OG title
        $config->twitterDescription($globalDefaults['twitter_description'] ?? null); // If null, SeoHandler defaults to OG description
        $config->twitterImageUrl($globalDefaults['twitter_image_url'] ?? null); // If null, SeoHandler defaults to OG image
        $config->twitterCard($globalDefaults['twitter_card'] ?? 'summary_large_image'); // Sensible hard default
        $config->twitterSite($globalDefaults['twitter_site'] ?? null); // e.g., '@YourHandle' or null
        $config->twitterCreator($globalDefaults['twitter_creator'] ?? null); // e.g., '@AuthorHandle' or null

        $schemaTypeClass = $globalSchemaDefaults['default_type'] ?? DefaultSchemaOrgWebPage::class;
        if (class_exists($schemaTypeClass) && is_subclass_of($schemaTypeClass, SchemaOrgBaseType::class)) {
            $config->schemaType(new $schemaTypeClass());
        } else {
            $config->schemaType(new DefaultSchemaOrgWebPage());
        }
        $config->schemaProperties([]);

        return $config;
    }
}
