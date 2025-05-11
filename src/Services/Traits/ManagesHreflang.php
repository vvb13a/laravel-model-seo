<?php

namespace Vvb13a\LaravelModelSeo\Services\Traits;

use Closure;
use Vvb13a\LaravelModelSeo\Services\Types\Hreflang;

trait ManagesHreflang
{
    public Hreflang|Closure|null $hreflang = null;

    public function hreflang(Hreflang|Closure|null $hreflang): self
    {
        $this->hreflang = $hreflang;
        return $this;
    }

    public function getHreflang(): ?Hreflang
    {
        return $this->evaluate($this->hreflang);
    }
}
