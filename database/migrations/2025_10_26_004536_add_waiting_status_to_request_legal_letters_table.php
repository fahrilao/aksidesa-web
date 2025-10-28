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
            // Drop the existing status column
            $table->dropColumn('status');
        });
        
        Schema::table('request_legal_letters', function (Blueprint $table) {
            // Add the new status column with 'Waiting' included and set as default
            $table->enum('status', ['Waiting', 'Pending', 'Processing', 'Completed'])->default('Waiting')->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('request_legal_letters', function (Blueprint $table) {
            // Drop the new status column
            $table->dropColumn('status');
        });
        
        Schema::table('request_legal_letters', function (Blueprint $table) {
            // Recreate the original status column
            $table->enum('status', ['Pending', 'Processing', 'Completed'])->default('Pending')->after('description');
        });
    }
};
