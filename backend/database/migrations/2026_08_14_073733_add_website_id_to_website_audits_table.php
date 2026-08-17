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
        Schema::table('website_audits', function (Blueprint $table) {
            $table->foreignId('website_id')->after('id')->nullable()->constrained('websites')->cascadeOnDelete();
            $table->string('url')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('website_audits', function (Blueprint $table) {
            $table->dropForeign(['website_id']);
            $table->dropColumn('website_id');
            $table->string('url')->nullable(false)->change();
        });
    }
};
