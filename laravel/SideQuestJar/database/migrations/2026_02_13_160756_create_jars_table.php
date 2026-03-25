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
        Schema::create('jars', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->longText("description")->nullable();
            $table->string("picture")->default("");
            $table->foreignId("user_id")->constrained()->onDelete("cascade");
            $table->enum("type", ["public", "private", "friends_only", "invite_only", "offical"])->default("public");
            $table->foreignId("created_by")->nullable()->constrained('users','id');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jars');
    }
};
