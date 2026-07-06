<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        $now = now();
        $oldRole = DB::table('roles')->where('role_name', 'Pasangan')->first();
        $homemakerRole = DB::table('roles')->where('role_name', 'Ibu Rumah Tangga')->first();

        if ($oldRole && $homemakerRole) {
            DB::table('users')->where('role_id', $oldRole->id)->update(['role_id' => $homemakerRole->id]);
            DB::table('roles')->where('id', $oldRole->id)->delete();
        } elseif ($oldRole) {
            DB::table('roles')->where('id', $oldRole->id)->update([
                'role_name' => 'Ibu Rumah Tangga',
                'description' => 'Mengelola transaksi harian serta memantau anggaran dan laporan keluarga.',
                'updated_at' => $now,
            ]);
        }

        foreach ([
            'Kepala Keluarga' => 'Mengelola seluruh transaksi, anggaran, anggota, dan pengaturan keluarga.',
            'Ibu Rumah Tangga' => 'Mengelola transaksi harian serta memantau anggaran dan laporan keluarga.',
            'Anak' => 'Melihat ringkasan keluarga dengan akses pencatatan yang terbatas.',
        ] as $name => $description) {
            DB::table('roles')->updateOrInsert(
                ['role_name' => $name],
                ['description' => $description, 'updated_at' => $now, 'created_at' => $now],
            );
        }

        $childRoleId = DB::table('roles')->where('role_name', 'Anak')->value('id');
        $unusedRoleIds = DB::table('roles')
            ->whereNotIn('role_name', ['Kepala Keluarga', 'Ibu Rumah Tangga', 'Anak'])
            ->pluck('id');

        if ($unusedRoleIds->isNotEmpty()) {
            DB::table('users')->whereIn('role_id', $unusedRoleIds)->update(['role_id' => $childRoleId]);
            DB::table('roles')->whereIn('id', $unusedRoleIds)->delete();
        }
    }

    public function down(): void
    {
        $homemakerRole = DB::table('roles')->where('role_name', 'Ibu Rumah Tangga')->first();

        if ($homemakerRole && ! DB::table('roles')->where('role_name', 'Pasangan')->exists()) {
            DB::table('roles')->where('id', $homemakerRole->id)->update([
                'role_name' => 'Pasangan',
                'updated_at' => now(),
            ]);
        }
    }
};
