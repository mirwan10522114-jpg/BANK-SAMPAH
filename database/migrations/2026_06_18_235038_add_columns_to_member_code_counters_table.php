<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('member_code_counters', function (Blueprint $table) {
            if (! Schema::hasColumn('member_code_counters', 'prefix')) {
                $table->string('prefix', 10)->unique()->after('id');
            }

            if (! Schema::hasColumn('member_code_counters', 'last_number')) {
                $table->unsignedInteger('last_number')->default(0)->after('prefix');
            }
        });
    }

    public function down(): void
    {
        Schema::table('member_code_counters', function (Blueprint $table) {
            $table->dropColumn(['prefix', 'last_number']);
        });
    }
};