<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
    ];


    // --- Relationship ---

    /**
     * Ang isang Category ay mayroong maraming Products (One-to-Many: Has Many)
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}