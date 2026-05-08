<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('owner_carts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('owner_id')->index();
            $table->json('items')->nullable();
            $table->timestamps();

            $table->unique('owner_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('owner_carts');
    }
};
