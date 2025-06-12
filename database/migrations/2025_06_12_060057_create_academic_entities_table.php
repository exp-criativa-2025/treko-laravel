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
        Schema::create('academic_entities', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('fantasy_name');
            $table->string('cnpj')->unique();
            $table->date('foundation_date');
            $table->string('status');
            $table->string('cep');
            
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academic_entities');
    }
};
