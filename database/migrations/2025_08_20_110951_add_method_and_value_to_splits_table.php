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
        Schema::table('splits', function (Blueprint $table) {
            $table->enum('method', ['equal','unequal','percentage','shares','adjustment'])->default('equal');
            $table->decimal('value', 10, 2)->nullable()->comment('percentage/share/amount');
        });
    }

    public function down(): void
    {
        Schema::table('splits', function (Blueprint $table) {
            $table->dropColumn(['method', 'value']);
        });
    }
};
