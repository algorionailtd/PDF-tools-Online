<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'name',
    'email',
    'password',
    'email_verified_at',
    'pdf_free_uploads_used',
    'premium_expires_at',
    'pdf_premium',
    'pdf_premium_at',
])]

#[Hidden([
    'password',
    'remember_token',
])]

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;


    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [

            /*
            |--------------------------------------------------------------------------
            | Email Verification
            |--------------------------------------------------------------------------
            */

            'email_verified_at' => 'datetime',


            /*
            |--------------------------------------------------------------------------
            | Password
            |--------------------------------------------------------------------------
            */

            'password' => 'hashed',


            /*
            |--------------------------------------------------------------------------
            | Global Free PDF Uploads
            |--------------------------------------------------------------------------
            |
            | Normal user gets 4 free files across ALL PDF tools.
            |
            | Example:
            |
            | Merge   = 2
            | Split   = 1
            | Compress = 1
            |
            | Total = 4
            |
            */

            'pdf_free_uploads_used' => 'integer',


            /*
            |--------------------------------------------------------------------------
            | Monthly Premium Expiry
            |--------------------------------------------------------------------------
            |
            | Example:
            |
            | 30 Sep 2026 + 1 month
            | = 30 Oct 2026
            |
            */

            'premium_expires_at' => 'datetime',


            /*
            |--------------------------------------------------------------------------
            | Old Premium Compatibility
            |--------------------------------------------------------------------------
            |
            | These fields are kept because your existing project
            | already uses them.
            |
            */

            'pdf_premium' => 'boolean',

            'pdf_premium_at' => 'datetime',
        ];
    }
}