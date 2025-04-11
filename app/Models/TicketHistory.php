<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TicketHistory extends Model
{
    use SoftDeletes,HasFactory;

    protected $fillable = [
        'field_changed',
        'old_value',
        'new_value',
        'ticket_id',
        'changed_by',
    ];

    // Relación con el modelo Ticket (un ticket puede tener muchos cambios)
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    // Relación con el modelo User (un cambio es realizado por un solo usuario)
    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
