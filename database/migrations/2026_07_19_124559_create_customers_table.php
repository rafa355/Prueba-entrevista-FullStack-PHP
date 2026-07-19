<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->string('dni', 20)->primary();
            $table->unsignedInteger('id_reg');
            $table->unsignedInteger('id_com');
            $table->string('email', 191)->unique();
            $table->string('name');
            $table->string('last_name');
            $table->string('address')->nullable();
            $table->dateTime('date_reg');
            $table->enum('status', ['A', 'I', 'trash'])->default('A');
            $table->engine = 'MyISAM';
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
