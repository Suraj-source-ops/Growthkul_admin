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
        Schema::table('product_trackings', function (Blueprint $table) {
            $table->unsignedBigInteger('stage_id')->after('product_id')->nullable();
            $table->unsignedBigInteger('updated_by')->after('notes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_trackings', function (Blueprint $table) {
            $table->dropColumn('stage_id');
            $table->dropColumn('updated_by');
        });
    }
};
