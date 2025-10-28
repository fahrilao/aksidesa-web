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
        Schema::table('request_legal_letters', function (Blueprint $table) {
            $table->string('name')->after('title');
            $table->string('nik', 16)->after('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('request_legal_letters', function (Blueprint $table) {
            $table->dropColumn(['name', 'nik']);
        });
    }
};
