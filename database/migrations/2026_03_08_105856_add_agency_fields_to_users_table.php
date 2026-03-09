<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('agent_code')->unique()->after('id');
            $table->string('role')->default('agent')->after('password');
            $table->unsignedTinyInteger('accreditation_level')->default(1)->after('role');
            $table->boolean('is_active')->default(true)->after('accreditation_level');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['agent_code', 'role', 'accreditation_level', 'is_active']);
        });
    }
};
