<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->string('dni', 45);
            $table->unsignedInteger('id_reg');
            $table->unsignedInteger('id_com');
            $table->string('email', 120)->unique();
            $table->string('password');
            $table->string('name', 45);
            $table->string('last_name', 45);
            $table->string('address')->nullable();
            $table->dateTime('date_reg');
            $table->enum('status', ['A', 'I', 'trash'])->default('A');
            $table->primary(['dni', 'id_reg', 'id_com']);
            $table->index(['id_com', 'id_reg']);
            $table->engine = 'MyISAM';
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
