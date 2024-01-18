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
        Schema::create('fund_duplications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('original_fund_id')->constrained('funds');
            $table->foreignId('duplicate_fund_id')->constrained('funds');
            $table->boolean('is_revised')->default(false);
            $table->timestamps();
            $table->unique(['original_fund_id', 'duplicate_fund_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fund_duplications');
    }
};
