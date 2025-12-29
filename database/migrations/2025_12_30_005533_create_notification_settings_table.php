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
        Schema::create('notification_settings', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->text('password'); // Will be encrypted
            $table->string('host')->default('mail.artrustees.com.my');
            $table->integer('port')->default(587);
            $table->string('username')->nullable(); // Often same as email
            $table->string('encryption')->default('tls');
            $table->string('from_name')->nullable();
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_settings');
    }
};
