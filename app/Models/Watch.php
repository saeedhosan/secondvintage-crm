<?php

declare(strict_types=1);

namespace App\Models;

use App\Observers\WatchObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Watch Model - Luxury watch inventory management
 */
#[ObservedBy(WatchObserver::class)]
class Watch extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'sku',
        'name',
        'serial_number',
        'reference',
        'case_size',
        'wrist_size',
        'caliber',
        'timegrapher',
        'cost_original',
        'cost_euro',
        'cost_currency',
        'cost_currency_rate',
        'cost_currency_rate_date',
        'cost_purchase_date',
        'status',
        'stage',
        'ai_instructions',
        'ai_thread_id',
        'ai_status',
        'ai_message',
        'notes',
        'description',
        'location',
        'seller_id',
        'agent_id',
        'brand_id',
        'user_id',
        'updated_by',
        'batch_id',
        'platform',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = ['image_urls', 'ai_image_urls'];

    /**
     * Scope to filter watches by status name.
     */
    public function scopeWhereStatus(Builder $query, string $status): Builder
    {
        return $query->whereHas('status', fn (Builder $q) => $q->where('name', $status));
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'sku';
    }

    /**
     * Get the user who created the watch.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the brand of the watch.
     */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * Get the status of the watch.
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    /**
     * Get the processing stage of the watch.
     */
    public function stage(): BelongsTo
    {
        return $this->belongsTo(Stage::class);
    }

    /**
     * Get the batch this watch belongs to.
     */
    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class);
    }

    /**
     * Get the current location of the watch.
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location', 'id');
    }

    /**
     * Get the agent assigned to this watch.
     */
    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    /**
     * Get the seller of this watch.
     */
    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    /**
     * Get the user who last updated the watch.
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get all images for this watch.
     */
    public function images(): HasMany
    {
        return $this->hasMany(WatchImage::class)->orderBy('order_index', 'asc');
    }

    /**
     * Get all activity logs for this watch.
     */
    public function logs(): HasMany
    {
        return $this->hasMany(WatchLog::class);
    }

    /**
     * Get all platform data for this watch.
     */
    public function platforms(): HasMany
    {
        return $this->hasMany(PlatformData::class);
    }

    /**
     * Get all transactions for this watch.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Get all image URLs for the watch.
     */
    public function getImageUrlsAttribute(): array
    {
        return $this->images->pluck('full_url')->all();
    }

    /**
     * Get all image URLs suitable for AI processing.
     */
    public function getAiImageUrlsAttribute(): array
    {
        return $this->images
            ->filter(fn ($image) => $image->use_for_ai)
            ->pluck('full_url')
            ->all();
    }

    /**
     * Bootstrap the model and its traits.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($watch) {
            if (empty($watch->cost_purchase_date)) {
                $watch->cost_purchase_date = now()->toDateString();
            }
        });
    }

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'cost_original'           => 'decimal:2',
            'cost_euro'               => 'decimal:2',
            'cost_purchase_date'      => 'date',
            'cost_currency_rate_date' => 'date',
        ];
    }
}
