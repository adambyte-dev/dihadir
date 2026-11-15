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
        Schema::table('attendances', function (Blueprint $table) {
            $table->string('nip', 64)->nullable()->change();
            $table->string('name')->nullable()->change();
            $table->string('unit_kerja')->nullable()->change();
            $table->string('kabupaten_kota')->nullable()->change();
            $table->string('signature_path')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->string('nip', 32)->nullable(false)->change();
            $table->string('name')->nullable(false)->change();
            $table->string('unit_kerja')->nullable(false)->change();
            $table->string('kabupaten_kota')->nullable(false)->change();
            $table->string('signature_path')->nullable(false)->change();
        });
    }
};
