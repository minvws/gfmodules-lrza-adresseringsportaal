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
        Schema::create('kvks', function (Blueprint $table) {
            $table->uuid('id')->unique()->primary();
            $table->string('kvk')->unique();
            $table->timestamps();
        });

        Schema::create('uras', function (Blueprint $table) {
            $table->uuid('id')->unique()->primary();
            $table->string('ura')->unique();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        Schema::create('suppliers', function (Blueprint $table) {
            $table->uuid('id')->unique()->primary();
            $table->string('endpoint', 1024);
            $table->timestamps();

            $table->foreignUuid('ura_id')->nullable()->constrained("uras")->references('id')->on('uras')->onDelete('cascade');
            $table->foreignUuid('kvk_id')->nullable()->constrained("kvk")->references('id')->on('kvks')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('uras');
        Schema::dropIfExists('kvks');
        Schema::dropIfExists('suppliers');
    }
};
