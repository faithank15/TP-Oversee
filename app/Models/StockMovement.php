<?php

namespace App\Models;

use App\Enums\MovementType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LogicException;

#[Fillable(['product_id', 'user_id', 'type', 'quantity', 'stock_before', 'stock_after', 'note'])]
class StockMovement extends Model
{
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, mixed>
     */
    protected function casts(): array
    {
        return ['type' => MovementType::class];
    }

    /**
     * Historique immuable : toute tentative de modification ou de suppression échoue.
     */
    protected static function booted(): void
    {
        static::updating(fn () => throw new LogicException('Un mouvement ne peut pas être modifié.'));
        static::deleting(fn () => throw new LogicException('Un mouvement ne peut pas être supprimé.'));
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mouvements des produits du compte, y compris ceux des produits supprimés.
     */
    public function scopeOwnedBy(Builder $query, User $user): Builder
    {
        return $query->whereHas('product', fn (Builder $query) => $query->withTrashed()->where('user_id', $user->id));
    }

    public function scopeOfType(Builder $query, ?string $type): Builder
    {
        return $query->when($type, fn (Builder $query) => $query->where('type', $type));
    }

    public function scopeBetween(Builder $query, ?string $from, ?string $to): Builder
    {
        return $query
            ->when($from, fn (Builder $query) => $query->whereDate('created_at', '>=', $from))
            ->when($to, fn (Builder $query) => $query->whereDate('created_at', '<=', $to));
    }
}
