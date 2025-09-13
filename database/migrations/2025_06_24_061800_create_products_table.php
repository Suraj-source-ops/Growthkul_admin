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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('client_id')->nullable();
            $table->tinyInteger('product_type')->nullable()->default(1)->comment('1 = size chart 2 = tech pack');
            $table->date('due_date')->nullable();
            $table->string('product_code')->nullable();
            $table->text('product_description')->nullable();
            $table->text('graphic_type')->nullable();
            $table->unsignedInteger('assigned_team')->nullable();
            $table->unsignedInteger('assigned_member')->nullable();
            $table->unsignedInteger('product_status')->default(0)->comment('0=pending 1=InProgress 2=OnHold 3=Completed')->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
