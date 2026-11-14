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
        Schema::create('meeting_form_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meeting_id')->constrained('meetings')->onDelete('cascade');
            $table->string('field_key', 64);
            $table->string('label');
            $table->string('type', 32); // text, textarea, number, select, radio, checkbox, file
            $table->text('description')->nullable();
            $table->string('placeholder')->nullable();
            $table->boolean('is_required')->default(false);
            $table->json('options')->nullable(); // Pilihan dropdown/radio/checkbox
            $table->integer('order_index')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['meeting_id', 'order_index']);
            $table->unique(['meeting_id', 'field_key']);
        });

        Schema::create('attendance_custom_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_id')->constrained('attendances')->onDelete('cascade');
            $table->foreignId('meeting_form_field_id')->nullable()->constrained('meeting_form_fields')->onDelete('set null');
            $table->string('field_key', 64);
            $table->string('field_label');
            $table->text('answer_value')->nullable();
            $table->string('file_path')->nullable(); // Untuk upload foto / berkas bukti
            $table->timestamps();

            $table->index(['attendance_id', 'field_key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_custom_answers');
        Schema::dropIfExists('meeting_form_fields');
    }
};
