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
        Schema::create('likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_id")->constrained()->onDelete("cascade");
            $table->enum("type", ["jar", "quest", "completion"]);
            $table->foreignId("quest_id")->nullable()->constrained()->onDelete("cascade");
            $table->foreignId("jar_id")->nullable()->constrained()->onDelete("cascade");
            $table->foreignId("quest_completion_id")->nullable()->constrained('quest_completions')->onDelete("cascade");
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('likes');
    }
};
