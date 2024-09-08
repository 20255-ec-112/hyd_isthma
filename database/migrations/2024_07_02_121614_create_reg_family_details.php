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
        Schema::create('reg_family_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registration_id')->constrained();
            $table->string("type")->nullable()->comment("mehram/children");
            $table->string("name")->nullable();
            $table->string("age")->nullable();
            $table->string("gender")->nullable();
            $table->string("fees")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reg_family_details');
    }
};