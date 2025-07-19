<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('expenses', function (Blueprint $table) {
        $table->dropForeign(['category_id']); // ⚠️ Important: drop foreign key first
        $table->dropColumn('category_id');    // ✅ Now drop the column
    });
}

public function down()
{
    Schema::table('expenses', function (Blueprint $table) {
        $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('set null');
    });
}

};
