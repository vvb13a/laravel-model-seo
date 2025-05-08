<?php

namespace Vvb13a\LaravelModelSeo\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SeoData extends Model
{
    protected $table = 'seo_data';

    protected $fillable = [
        'title',
        'description',
        'keywords',
        'canonical_url',
        'robots',
    ];

    public function seoable(): MorphTo
    {
        return $this->morphTo();
    }
}
