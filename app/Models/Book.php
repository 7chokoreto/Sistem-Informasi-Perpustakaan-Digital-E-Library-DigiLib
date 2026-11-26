<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'book_code',
        'category_id',
        'title',
        'author',
        'category',
        'publisher',
        'publication_year',
        'edition',
        'physical_description',
        'language',
        'isbn',
        'shelf',
        'location',
        'stock',
        'read_in_place_stock',
        'synopsis',
        'cover',
    ];

    public function categoryRelation(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function isFavoritedBy(?User $user): bool
    {
        if (!$user) return false;
        return $this->favorites()->where('user_id', $user->id)->exists();
    }

    public function isInCartOf(?User $user): bool
    {
        if (!$user) return false;
        return $this->cartItems()->where('user_id', $user->id)->exists();
    }
}
