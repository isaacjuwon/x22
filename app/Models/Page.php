<?php

namespace App\Models;

use App\Enums\PageStatus;
use App\Concerns\HasContentMedia;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;

#[Fillable(['user_id', 'title', 'slug', 'excerpt', 'content', 'status', 'published_at', 'meta_description', 'og_image'])]
class Page extends Model implements HasMedia
{
    use HasContentMedia;
    use HasFactory;
    use SoftDeletes;

    protected function casts(): array
    {
        return [
            'status'       => PageStatus::class,
            'published_at' => 'datetime',
            'deleted_at'   => 'datetime',
        ];
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', PageStatus::Published->value)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function scopeDraft(Builder $query): Builder
    {
        return $query->where('status', PageStatus::Draft->value);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
