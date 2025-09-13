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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique()->comment('Name of the product\'s graphic types');
            $table->timestamp('created_at')->useCurrent()->comment('Timestamp when the record was created');
            $table->timestamp('updated_at')->useCurrent()->nullable()->comment('Timestamp when the record was last updated');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
