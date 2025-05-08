<?php

namespace Vvb13a\LaravelModelSeo\Config\Traits;

use Closure;

trait ManagesTwitterCardFallbacks
{
    public string|Closure|null $twitterTitleFallback = null;

    public string|Closure|null $twitterDescriptionFallback = null;

    public string|Closure|null $twitterImageUrlFallback = null;

    public string|Closure|null $twitterCardTypeFallback = null;

    public string|Closure|null $twitterSiteHandleFallback = null;

    public string|Closure|null $twitterCreatorHandleFallback = null;

    public function twitterTitle(string|Closure|null $twitterTitle): self
    {
        $this->twitterTitleFallback = $twitterTitle;

        return $this;
    }

    public function twitterDescription(string|Closure|null $twitterDescription): self
    {
        $this->twitterDescriptionFallback = $twitterDescription;

        return $this;
    }

    public function twitterImageUrl(string|Closure|null $twitterImageUrl): self
    {
        $this->twitterImageUrlFallback = $twitterImageUrl;

        return $this;
    }

    public function twitterCard(string|Closure|null $twitterCard): self
    {
        $this->twitterCardTypeFallback = $twitterCard;

        return $this;
    }

    public function twitterSite(string|Closure|null $twitterSite): self
    {
        $this->twitterSiteHandleFallback = $twitterSite;

        return $this;
    }

    public function twitterCreator(string|Closure|null $twitterCreator): self
    {
        $this->twitterCreatorHandleFallback = $twitterCreator;

        return $this;
    }
}
