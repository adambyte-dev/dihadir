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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meeting_id')->constrained('meetings')->onDelete('cascade');
            $table->foreignId('participant_id')->nullable()->constrained('participants')->onDelete('set null');
            $table->string('nip', 32)->index();
            $table->string('name');
            $table->string('unit_kerja');
            $table->string('kabupaten_kota');
            $table->string('signature_path');
            $table->date('attended_date');
            $table->time('attended_time');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('status', 24)->default('valid'); // valid, flagged, cancelled
            $table->timestamps();

            // Anti-duplicate constraint: 1 NIP hanya dapat mengisi 1 kali pada 1 pertemuan
            $table->unique(['meeting_id', 'nip']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
