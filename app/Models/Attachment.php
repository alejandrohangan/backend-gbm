<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attachment extends Model
{
    use SoftDeletes,HasFactory;

    protected $fillable = ['file_path', 'ticket_id', 'uploaded_by'];

    // Un adjunto pertenece a un solo ticket (1:N)
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    // Un adjunto fue subido por un usuario (1:N)
    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
