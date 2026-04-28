<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_locked')->default(false)->after('password');
            $table->timestamp('locked_at')->nullable()->after('is_locked');
            $table->string('last_login_at')->nullable()->after('locked_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_locked', 'locked_at', 'last_login_at']);
        });
    }
};
