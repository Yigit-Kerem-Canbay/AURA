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
            $table->integer('security_score')->nullable()->after('seo_score');
            $table->integer('accessibility_score')->nullable()->after('performance_score');
            $table->integer('total_score')->nullable()->after('accessibility_score');
            $table->text('ai_summary')->nullable();
            $table->text('cross_intelligence_report')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('website_audits', function (Blueprint $table) {
            $table->dropColumn([
                'security_score',
                'accessibility_score',
                'total_score',
                'ai_summary',
                'cross_intelligence_report'
            ]);
        });
    }
};
