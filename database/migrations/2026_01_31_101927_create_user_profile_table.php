<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id', false)->primary();
            $table->string('first_name', 50)->index();
            $table->string('last_name', 50)->index()->nullable();
            $table->string('phone')->nullable()->unique();
            $table->text('address')->nullable();
            $table->unsignedTinyInteger('gender_id')->default(0);
            $table->string('photo', 100)->nullable()->default('no_photo.jpg');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete()->cascadeOnUpdate();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};
