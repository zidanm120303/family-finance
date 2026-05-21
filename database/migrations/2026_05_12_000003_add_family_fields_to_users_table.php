<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('family_id')->nullable()->after('id')->constrained('families')->nullOnDelete();
            $table->foreignId('role_id')->nullable()->after('family_id')->constrained('roles')->nullOnDelete();
            $table->string('username')->nullable()->unique()->after('email');
            $table->string('phone')->nullable()->after('password');
            $table->string('photo')->nullable()->after('phone');
            $table->boolean('is_active')->default(true)->after('photo');
            $table->timestamp('last_login')->nullable()->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('family_id');
            $table->dropConstrainedForeignId('role_id');
            $table->dropColumn(['username', 'phone', 'photo', 'is_active', 'last_login']);
        });
    }
};
