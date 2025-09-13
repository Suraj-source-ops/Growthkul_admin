<?php

use App\Models\MasterProductStages;
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
        Schema::table('master_product_stages', function (Blueprint $table) {
            $table->unsignedInteger('sequence')->default(0)->after('id');
            $table->tinyInteger('is_active')->default(1)->after('type');
        });

        // Add initial positions for existing stages
        $stages = MasterProductStages::orderBy('created_at')->get();
        foreach ($stages as $index => $stage) {
            $stage->update(['sequence' => $index + 1]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_product_stages', function (Blueprint $table) {
            $table->dropColumn('sequence');
            $table->dropColumn('is_active');
        });
    }
};
