<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('favorite_gifs', function (Blueprint $table): void {
            $table->id();
            $table->string('gif_id', 100);
            $table->string('alias');
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'gif_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('favorite_gifs');
    }
};
