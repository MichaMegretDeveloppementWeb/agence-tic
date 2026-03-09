<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->foreignId('report_id')->nullable()->after('category_id')->constrained()->nullOnDelete();
        });

        Schema::table('reports', function (Blueprint $table) {
            $table->renameColumn('history', 'notes');
        });
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropConstrainedForeignId('report_id');
        });

        Schema::table('reports', function (Blueprint $table) {
            $table->renameColumn('notes', 'history');
        });
    }
};
