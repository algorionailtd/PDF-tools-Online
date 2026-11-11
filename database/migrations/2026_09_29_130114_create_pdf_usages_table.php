
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pdf_usages', function (Blueprint $table) {
            $table->id();

            // User who owns this usage record
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            // Total free files used across ALL 12 PDF tools
            $table->unsignedInteger('free_uploads_used')
                ->default(0);

            $table->timestamps();

            // One usage record per user
            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pdf_usages');
    }
};

