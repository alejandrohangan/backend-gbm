<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends Model
{
    
    use SoftDeletes,HasFactory;

    protected $fillable = ['title', 'description', 'status', 'started_at', 'closed_at', 'priority_id', 'category_id', 'requester_id', 'agent_id'];

    // Relación con la tabla 'priorities' (N:1)
    public function priority(): BelongsTo
    {
        return $this->belongsTo(Priority::class);
    }

    // Relación con la tabla 'categories' (N:1)
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    // Relación con la tabla 'users' para el requester (N:1), un mismo ticket solo puede ser creado por una persona
    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    // Relación con la tabla 'users' para el agent (N:1), un mismo ticket solo puede ser atendido por una persona
    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    // Relación de uno a muchos con TicketHistory
    public function ticketHistories():HasMany
    {
        return $this->hasMany(TicketHistory::class);
    }

    // Relación N:M con la tabla 'tags' a través de la tabla intermedia 'tag_ticket'
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'tag_ticket');
    }

    public function attachments(): HasMany {
        return $this->hasMany(Attachment::class);
    }
}
