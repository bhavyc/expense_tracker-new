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
        Schema::table('admob_reports', function (Blueprint $table) {
          $table->bigInteger('clicks')->default(0)->after('impressions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admob_reports', function (Blueprint $table) {
            //
        });
    }
};
