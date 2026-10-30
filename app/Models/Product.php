<?php

namespace App\Models;

use App\Enums\Unit;
use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 'quantity' est volontairement absente de $fillable : seul StockService la modifie.
 */
#[Fillable(['category_id', 'reference', 'name', 'description', 'unit', 'price', 'alert_threshold'])]
class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory, SoftDeletes;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, mixed>
     */
    protected function casts(): array
    {
        return [
            'unit' => Unit::class,
            'price' => 'integer',
            'quantity' => 'integer',
            'alert_threshold' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function movements(): HasMany
    {
        return $this->hasMany(StockMovement::class)->latest();
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        return $query->when($term, fn (Builder $query) => $query->where(
            fn (Builder $query) => $query
                ->where('name', 'like', "%{$term}%")
                ->orWhere('reference', 'like', "%{$term}%")
        ));
    }

    public function scopeInCategory(Builder $query, int|string|null $categoryId): Builder
    {
        return $query->when($categoryId, fn (Builder $query) => $query->where('category_id', $categoryId));
    }

    public function scopeLowStock(Builder $query): Builder
    {
        return $query->whereColumn('quantity', '<=', 'alert_threshold');
    }

    public function scopeStockStatus(Builder $query, ?string $status): Builder
    {
        return match ($status) {
            'out' => $query->where('quantity', 0),
            'low' => $query->lowStock()->where('quantity', '>', 0),
            'ok' => $query->whereColumn('quantity', '>', 'alert_threshold'),
            default => $query,
        };
    }

    protected function stockValue(): Attribute
    {
        return Attribute::get(fn () => $this->quantity * $this->price);
    }

    protected function isLowStock(): Attribute
    {
        return Attribute::get(fn () => $this->quantity <= $this->alert_threshold);
    }

    protected function isOutOfStock(): Attribute
    {
        return Attribute::get(fn () => $this->quantity === 0);
    }
}
