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
        Schema::table('resume_scores', function (Blueprint $table) {
            // Secteur détecté automatiquement
            $table->string('detected_sector')->nullable()->after('matched_keywords');

            // Détail des scores par composant
            $table->json('score_breakdown')->nullable()->after('detected_sector');

            // Expérience du candidat et requise
            $table->integer('candidate_experience')->default(0)->after('score_breakdown');
            $table->integer('required_experience')->default(0)->after('candidate_experience');

            // Données d'analyse complètes
            $table->json('analysis_data')->nullable()->after('required_experience');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('resume_scores', function (Blueprint $table) {
            $table->dropColumn([
                'detected_sector',
                'score_breakdown',
                'candidate_experience',
                'required_experience',
                'analysis_data'
            ]);
        });
    }
};