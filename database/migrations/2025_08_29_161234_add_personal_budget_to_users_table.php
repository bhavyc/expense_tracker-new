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
               $table->decimal('personal_budget', 10, 2)->default(0)->after('role');
        $table->decimal('personal_carry_forward_balance', 10, 2)->default(0)->after('personal_budget');
        });
    }

    
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['personal_budget', 'personal_carry_forward_balance']);
        });
    }
};
