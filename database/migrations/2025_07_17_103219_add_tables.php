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
        $table->decimal('lent_total', 10, 2)->default(0)->after('password');
        $table->decimal('owed_total', 10, 2)->default(0)->after('lent_total');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::table('users', function (Blueprint $table) {
        $table->dropColumn(['lent_total', 'owed_total']);
    });
    }
};
