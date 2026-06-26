<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add roles column if not already present (idempotent — first run may have partially applied)
        if (! Schema::hasColumn('users', 'roles')) {
            Schema::table('users', function (Blueprint $table) {
                $table->json('roles')->nullable()->after('email');
            });
        }

        // Migrate existing single role → JSON array for any rows not yet migrated
        if (Schema::hasColumn('users', 'role')) {
            DB::table('users')->orderBy('id')->each(function ($user) {
                if (is_null($user->roles)) {
                    DB::table('users')
                        ->where('id', $user->id)
                        ->update(['roles' => json_encode([$user->role])]);
                }
            });

            Schema::table('users', function (Blueprint $table) {
                if (Schema::hasIndex('users', 'users_role_index')) {
                    $table->dropIndex(['role']);
                }
                $table->dropColumn('role');
            });
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('nasabah')->after('email');
            $table->index('role');
        });

        DB::table('users')->orderBy('id')->each(function ($user) {
            $roles = json_decode($user->roles, true);
            DB::table('users')
                ->where('id', $user->id)
                ->update(['role' => $roles[0] ?? 'nasabah']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('roles');
        });
    }
};
