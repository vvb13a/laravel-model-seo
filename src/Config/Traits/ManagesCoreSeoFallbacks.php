<?php

namespace Vvb13a\LaravelModelSeo\Config\Traits;

use Closure;

trait ManagesCoreSeoFallbacks
{
    public string|Closure|null $titleFallback = null;
    public string|Closure|null $descriptionFallback = null;
    public string|Closure|null $keywordsFallback = null;
    public string|Closure|null $canonicalUrlFallback = null;
    public string|Closure|null $robotsDirectiveFallback = null;

    public function title(string|Closure|null $title): self
    {
        $this->titleFallback = $title;
        return $this;
    }

    public function description(string|Closure|null $description): self
    {
        $this->descriptionFallback = $description;
        return $this;
    }

    public function keywords(string|Closure|null $keywords): self
    {
        $this->keywordsFallback = $keywords;
        return $this;
    }

    public function canonicalUrl(string|Closure|null $canonicalUrl): self
    {
        $this->canonicalUrlFallback = $canonicalUrl;
        return $this;
    }

    public function robots(string|Closure|null $robots): self
    {
        $this->robotsDirectiveFallback = $robots;
        return $this;
    }
}
