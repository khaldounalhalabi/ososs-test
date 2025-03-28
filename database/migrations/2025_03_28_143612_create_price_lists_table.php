<?php

use App\Models\Product;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('price_lists', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Product::class)->constrained()->cascadeOnDelete();
            $table->string('country_code')->nullable();
            $table->foreign('country_code')->references('code')->on('countries')->cascadeOnDelete();

            $table->string('currency_code')->nullable();
            $table->foreign('currency_code')->references('code')->on('currencies')->cascadeOnDelete();

            $table->double('price')->default(0.0000);

            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();

            $table->integer('priority')->default(0);

            $table->unique(['product_id', 'country_code', 'currency_code', 'start_date', 'end_date', 'priority']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('price_lists');
    }
};
