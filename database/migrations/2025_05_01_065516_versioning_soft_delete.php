<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add versioning and soft delete columns to the 'suppliers' table
        Schema::table('suppliers', function (Blueprint $table) {
            $table->integer('version')->default(1)->after('id');
            $table->timestamp('deleted_at')->nullable()->after('updated_at');
        });

        // Modify the primary key to include 'id' and 'version'
        DB::statement('
            ALTER TABLE public.suppliers
            DROP CONSTRAINT suppliers_pkey,
            ADD CONSTRAINT suppliers_pkey PRIMARY KEY (id, version);
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert the primary key to only include 'id'
        DB::statement('
            ALTER TABLE public.suppliers
            DROP CONSTRAINT suppliers_pkey,
            ADD CONSTRAINT suppliers_pkey PRIMARY KEY (id);
        ');

        // Remove versioning and soft delete columns from the 'suppliers' table
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn('version');
            $table->dropColumn('deleted_at');
        });
    }
};
