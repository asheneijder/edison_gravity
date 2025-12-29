<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('swift_messages', function (Blueprint $table) {
            $table->id();
            $table->string('frm_BIC')->nullable();
            $table->string('to_BIC')->nullable();
            $table->json('messages')->nullable(); // Stores the parsed data
            $table->timestamp('system_datime')->useCurrent(); // Default to now

            // Extra useful fields
            $table->string('type')->nullable(); // MT103, etc
            $table->string('source_file')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('swift_messages');
    }
};
