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
            $table->id('id');
            $table->string('sku', 100)->unique();
            $table->decimal('price', 10, 2)->default(0);
            $table->integer('stock')->default(0)->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('category_id')
                ->constrained('categories', 'id')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->timestamps();
        });

        Schema::create('products_translations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('slug');
            $table->string('locale')->index();
            $table->unique(['products_id', 'locale']);

            $table->foreign('products_id')
                ->references('id')
                ->on('products')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products_translations');
        Schema::dropIfExists('products');
    }
};
