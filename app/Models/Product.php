<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * Ito ang mga columns na pwedeng i-update gamit ang $product->create($request->all())
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',       // Foreign key para sa User (kung sino ang nag-upload)
        'category_id',   // Foreign key para sa Category
        'name',
        'description',
        'price',
    ];


    // --- Relationships ---

    /**
     * Ang Product ay kabilang sa isang Category (Belongs To)
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Ang Product ay pagmamay-ari ng isang User (Belongs To)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}