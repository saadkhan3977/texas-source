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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('user_id')->nullable();
            $table->string('title')->nullable();
            $table->string('category')->nullable();
            $table->string('quantity')->nullable();
            $table->string('price')->nullable();
            $table->string('size')->nullable();
            $table->string('color')->nullable();
            $table->text('photo')->nullable();
            $table->enum('status',['active','inactive'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
