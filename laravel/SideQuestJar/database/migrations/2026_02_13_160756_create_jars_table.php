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
            //Korlatozas kell mint a kave shopnal lehessen (amerikai expresso es olasz expresso) name es created_by alapjan chekkol
            $table->id();
            $table->string("name");   
            $table->longText("description")->nullable();       
            $table->integer("likes")->default(0);
            $table->enum("type", ["public_jar", "private_jar", "friends_only_jar", "invite_only_jar", "official_jar"])->default("public_jar");
            $table->foreignId("created_by")->nullable()->constrained('users','id');
            $table->string("picture")->default("https://i.ibb.co/2YvZypwJ/Jar-Quest-Placeholder.png");
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
