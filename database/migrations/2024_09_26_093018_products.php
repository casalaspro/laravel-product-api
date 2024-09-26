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
        Schema::create('products', function (Blueprint $table){
            $table->id();
            $table->char('name', 255);
            $table->text('description')->nullable();
            $table->char('image', 255)->default('default.jpg');
            $table->decimal('price', 10, 2);
            $table->decimal('discount_percentage', 5, 2)->default(0.00);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('products');
    }
};
