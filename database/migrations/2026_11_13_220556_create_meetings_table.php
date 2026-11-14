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
        Schema::create('meetings', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('public_token', 64)->unique();
            $table->text('description')->nullable();
            $table->string('organizer')->default('SMKN 1 Majalengka');
            $table->string('location');
            $table->date('meeting_date');
            $table->time('start_time');
            $table->time('end_time')->nullable();
            $table->unsignedInteger('max_participants')->nullable();
            $table->string('status')->default('scheduled'); // draft, scheduled, ongoing, completed, archived
            $table->string('form_status')->default('draft'); // draft, published, closed
            $table->boolean('allow_anonymous')->default(false); // terbuka tanpa login NIP jika true
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('meeting_admins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meeting_id')->constrained('meetings')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['meeting_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meeting_admins');
        Schema::dropIfExists('meetings');
    }
};
