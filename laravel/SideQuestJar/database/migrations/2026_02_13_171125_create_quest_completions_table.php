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
        Schema::create('quest_completions', function (Blueprint $table) {
            $table->id();
            $table->dateTime("completion_date");
            $table->string("picture")->default("");
            $table->longText("description")->nullable();
            $table->integer("likes")->default(0);
            $table->enum("type", ["public_post", "private_post", "friends_only_post"])->default("public_post");
            $table->foreignId("user_id")->constrained()->onDelete("cascade");
            $table->foreignId("quest_id")->constrained()->onDelete("cascade");
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quest_completions');
    }
};
