<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PdfUsage extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'free_uploads_used',
    ];

    protected $casts = [
        'free_uploads_used' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}