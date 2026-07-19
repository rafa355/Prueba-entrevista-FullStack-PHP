<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('communes', function (Blueprint $table) {
            $table->increments('id_com');
            $table->unsignedInteger('id_reg');
            $table->string('description');
            $table->enum('status', ['A', 'I', 'trash'])->default('A');
            $table->engine = 'MyISAM';
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('communes');
    }
};
