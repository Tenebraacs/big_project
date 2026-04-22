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
        Schema::table('jars', function (Blueprint $table) {
            $table->foreignId('current_daily_quest_id')
                ->nullable()
                ->after('picture')
                ->constrained('jar_quests')
                ->nullOnDelete();

            $table->foreignId('previous_daily_quest_id')
                ->nullable()
                ->after('current_daily_quest_id')
                ->constrained('jar_quests')
                ->nullOnDelete();

            $table->date('current_daily_quest_date')
                ->nullable()
                ->after('previous_daily_quest_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jars', function (Blueprint $table) {
            $table->dropForeign(['current_daily_quest_id']);
            $table->dropForeign(['previous_daily_quest_id']);
            $table->dropColumn([
                'current_daily_quest_id',
                'previous_daily_quest_id',
                'current_daily_quest_date',
            ]);
        });
    }
};
