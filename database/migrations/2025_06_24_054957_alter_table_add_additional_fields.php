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
        Schema::table('users', function (Blueprint $table) {
            $table->string('mobile', 20)->after('email')->nullable();
            $table->unsignedInteger('team_id')->after('password')->nullable();
            $table->unsignedInteger('role_id')->after('team_id')->nullable();
            $table->unsignedInteger('file_id')->after('role_id')->nullable();
            $table->tinyInteger('status')->after('file_id')->default(1)->comment('1 = active, 0 = inactive');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['mobile', 'team_id', 'role_id', 'file_id', 'status']);
        });
    }
};
