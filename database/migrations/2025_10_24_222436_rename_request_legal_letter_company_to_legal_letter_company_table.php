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
        Schema::rename('request_legal_letter_company', 'legal_letter_company');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('legal_letter_company', 'request_legal_letter_company');
    }
};
