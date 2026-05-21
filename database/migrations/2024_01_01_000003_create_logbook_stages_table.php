<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('logbook_stages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('logbook_id')->constrained()->onDelete('cascade');
            $table->integer('stage_number');
            $table->json('data');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
            $table->unique(['logbook_id', 'stage_number']);
        });
    }
    public function down(): void { Schema::dropIfExists('logbook_stages'); }
};
