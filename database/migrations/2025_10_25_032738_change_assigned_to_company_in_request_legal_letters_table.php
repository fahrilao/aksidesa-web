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
            // Drop the old assigned_to column that references users
            $table->dropForeign(['assigned_to']);
            $table->dropColumn('assigned_to');
            
            // Add new assigned_company_id column that references companies
            $table->foreignId('assigned_company_id')->nullable()->constrained('companies')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('request_legal_letters', function (Blueprint $table) {
            // Drop the new assigned_company_id column
            $table->dropForeign(['assigned_company_id']);
            $table->dropColumn('assigned_company_id');
            
            // Restore the old assigned_to column that references users
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
        });
    }
};
