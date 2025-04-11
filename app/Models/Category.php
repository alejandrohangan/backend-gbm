<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'description'];

    // Una categoría puede estar asociada a muchos tickets (1:N)
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }
}