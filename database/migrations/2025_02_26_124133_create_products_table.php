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
            $table->string('name');
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete(); //nullable agar bisa saja tidak ada relasi pada category, nullondelete jika category terhapus maka product tidak akan terhapus
            $table->string('slug')->nullable();
            $table->integer('stock');
            $table->integer('price');
            $table->boolean('is_active')->default(true);
            $table->string('image')->nullable();
            $table->string('barcode')->nullable();
            $table->longtext('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
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
