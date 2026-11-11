<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

            $table->unsignedInteger('pdf_free_uploads_used')
                ->default(0)
                ->after('email');

            $table->boolean('pdf_premium')
                ->default(false)
                ->after('pdf_free_uploads_used');

            $table->timestamp('pdf_premium_at')
                ->nullable()
                ->after('pdf_premium');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'pdf_free_uploads_used',
                'pdf_premium',
                'pdf_premium_at',
            ]);
        });
    }
};