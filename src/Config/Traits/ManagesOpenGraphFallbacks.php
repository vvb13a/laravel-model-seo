<?php

namespace Vvb13a\LaravelModelSeo\Config\Traits;

use Closure;

trait ManagesOpenGraphFallbacks
{
    public string|Closure|null $ogTitleFallback = null;
    public string|Closure|null $ogDescriptionFallback = null;
    public string|Closure|null $ogImageUrlFallback = null;
    public string|Closure|null $ogTypeFallback = null;

    public function ogTitle(string|Closure|null $ogTitle): self
    {
        $this->ogTitleFallback = $ogTitle;
        return $this;
    }

    public function ogDescription(string|Closure|null $ogDescription): self
    {
        $this->ogDescriptionFallback = $ogDescription;
        return $this;
    }

    public function ogImageUrl(string|Closure|null $ogImage): self
    {
        $this->ogImageUrlFallback = $ogImage;
        return $this;
    }

    public function ogType(string|Closure|null $ogType): self
    {
        $this->ogTypeFallback = $ogType;
        return $this;
    }
}
