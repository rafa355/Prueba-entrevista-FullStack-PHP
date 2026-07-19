<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tokens', function (Blueprint $table) {
            $table->id();
            $table->string('token', 40)->unique();
            $table->string('email', 191);
            $table->dateTime('date_reg');
            $table->unsignedSmallInteger('ttl')->default(60);
            $table->enum('status', ['A', 'I', 'trash'])->default('A');
            $table->engine = 'MyISAM';
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tokens');
    }
};
