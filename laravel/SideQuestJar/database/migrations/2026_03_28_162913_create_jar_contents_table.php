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
        Schema::create('jar_contents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jar_id')->constrained()->onDelete('cascade');
            $table->string("title")->unique();
            $table->longText("description")->nullable();
            $table->string("picture")->default("");
            $table->integer("likes")->default(0);
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
        Schema::dropIfExists('jar_contents');
    }
};
