<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    use SoftDeletes;

    protected $fillable = ['name'];

    // Relación N:M entre Tag y Ticket
    public function tickets(): BelongsToMany
    {
        return $this->belongsToMany(Ticket::class);
    }
}