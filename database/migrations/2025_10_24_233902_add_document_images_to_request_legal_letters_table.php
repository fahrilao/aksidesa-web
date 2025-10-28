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
            $table->string('ktp_image_path')->nullable()->after('description');
            $table->string('kk_image_path')->nullable()->after('ktp_image_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('request_legal_letters', function (Blueprint $table) {
            $table->dropColumn(['ktp_image_path', 'kk_image_path']);
        });
    }
};
