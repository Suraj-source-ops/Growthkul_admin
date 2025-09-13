<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained(); //shorthend of => references('id')->on('products')
            $table->foreignId('user_id')->references('id')->on('users')->nullable();
            $table->string('action');
            $table->json('changes')->nullable();
            $table->text('note')->nullable();
            $table->bigInteger('assign_to')->nullable();
            $table->tinyInteger('status')->default(0)->comment('1=read 0=unread');
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('histories');
    }
};
