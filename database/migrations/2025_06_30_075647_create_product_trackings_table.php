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
        Schema::create('product_trackings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->string('product_stage');
            $table->date('estimate_date')->nullable();
            $table->tinyInteger('stage_type')->comment('1=File,2=toggle button')->nullable();
            $table->tinyInteger('status')->default(0)->comment('1=completed,0=pending');
            $table->text('notes')->nullable()->comment('product tracking notes');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_trackings');
    }
};
