<?php

namespace Vvb13a\LaravelModelSeo\Services;

use Closure;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use ReflectionClass;
use ReflectionMethod;
use Vvb13a\LaravelModelSeo\Services\Traits\HandlesClosureEvaluation;
use Vvb13a\LaravelModelSeo\Services\Traits\ManagesCoreTags;
use Vvb13a\LaravelModelSeo\Services\Traits\ManagesHreflang;
use Vvb13a\LaravelModelSeo\Services\Traits\ManagesOpenGraph;
use Vvb13a\LaravelModelSeo\Services\Traits\ManagesSchema;
use Vvb13a\LaravelModelSeo\Services\Traits\ManagesTwitter;

class Seo implements Htmlable
{
    use ManagesCoreTags;
    use ManagesOpenGraph;
    use ManagesTwitter;
    use ManagesHreflang;
    use ManagesSchema;
    use HandlesClosureEvaluation;

    public function __construct(protected Model $record)
    {
    }

    public static function make(Model $record): self
    {
        return app(static::class, ['record' => $record]);
    }

    public function toHtml()
    {
        return $this->render()->render();
    }

    public function render(): View
    {
        return view('model-seo::seo', $this->extractPublicMethods());
    }

    public function extractPublicMethods(): array
    {
        $reflection = new ReflectionClass($this);

        $methods = [];

        foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            $methods[$method->getName()] = Closure::fromCallable([$this, $method->getName()]);
        }

        return $methods;
    }
}
