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
        Schema::rename('request_legal_letters', 'legal_letters');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('legal_letters', 'request_legal_letters');
    }
};
