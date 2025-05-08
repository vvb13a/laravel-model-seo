<?php

namespace Vvb13a\LaravelModelSeo\Config\Traits;

use Closure;
use Spatie\SchemaOrg\BaseType;

trait ManagesSchemaOrgResolvers
{
    public BaseType|Closure|null $schemaTypeResolver = null;

    public array|Closure|null $schemaPropertiesResolver = null;

    public function schemaType(BaseType|Closure|null $schemaType): self
    {
        $this->schemaTypeResolver = $schemaType;

        return $this;
    }

    public function schemaProperties(array|Closure|null $schemaProperties): self
    {
        $this->schemaPropertiesResolver = $schemaProperties;

        return $this;
    }
}
