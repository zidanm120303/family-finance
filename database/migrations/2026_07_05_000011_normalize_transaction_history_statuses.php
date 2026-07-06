<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::table('transaction_histories')
            ->select(['id', 'old_data', 'new_data'])
            ->orderBy('id')
            ->chunkById(200, function ($histories): void {
                foreach ($histories as $history) {
                    $updates = [];

                    foreach (['old_data', 'new_data'] as $column) {
                        if (! $history->{$column}) {
                            continue;
                        }

                        $data = is_string($history->{$column})
                            ? json_decode($history->{$column}, true)
                            : (array) $history->{$column};

                        if (! is_array($data)) {
                            continue;
                        }

                        $normalized = $this->normalizeStatuses($data);

                        if ($normalized !== $data) {
                            $updates[$column] = json_encode($normalized, JSON_UNESCAPED_UNICODE);
                        }
                    }

                    if ($updates !== []) {
                        DB::table('transaction_histories')->where('id', $history->id)->update($updates);
                    }
                }
            });
    }

    public function down(): void
    {
        // Status lama sengaja tidak dipulihkan agar audit tetap konsisten
        // dengan dua status transaksi yang didukung aplikasi.
    }

    private function normalizeStatuses(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->normalizeStatuses($value);
                continue;
            }

            if ($key === 'status' && in_array($value, ['pending', 'draft'], true)) {
                $data[$key] = 'cancel';
            }
        }

        return $data;
    }
};
