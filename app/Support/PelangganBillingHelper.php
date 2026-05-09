<?php

namespace App\Support;

class PelangganBillingHelper
{
    private const NOMINAL_RUPIAH_PENUH_MIN = 10000;

    public static function toPositiveNumber(mixed $v, float|int $fallback = 0): float|int
    {
        $n = is_numeric($v) ? 0 + $v : NAN;

        return is_finite($n) ? $n : $fallback;
    }

    public static function expandRibuKeRupiahPelanggan(mixed $n): mixed
    {
        $x = self::toPositiveNumber($n, NAN);
        if (! is_finite($x)) {
            return $n;
        }
        if ($x <= 0) {
            return $x;
        }
        if ($x < self::NOMINAL_RUPIAH_PENUH_MIN) {
            return (int) round($x * 1000);
        }

        return (int) round($x);
    }

    /** @param array<string,mixed> $obj */
    public static function normalizePelangganNominalFields(array &$obj): void
    {
        if (array_key_exists('hargaPaket', $obj)) {
            $obj['hargaPaket'] = self::expandRibuKeRupiahPelanggan($obj['hargaPaket']);
        }
        if (array_key_exists('totalFinal', $obj)) {
            $obj['totalFinal'] = self::expandRibuKeRupiahPelanggan($obj['totalFinal']);
        }
        foreach (['biayaTambahan1', 'biayaTambahan2', 'diskon'] as $k) {
            if (! isset($obj[$k]) || ! is_array($obj[$k])) {
                continue;
            }
            $slot = &$obj[$k];
            if (array_key_exists('nominal', $slot)) {
                $slot['nominal'] = self::expandRibuKeRupiahPelanggan($slot['nominal']);
            }
        }
    }

    /** @param array<string,mixed> $pData */
    public static function inferBiayaTagihanDariPelanggan(array $pData): float|int
    {
        $biaya = self::toPositiveNumber($pData['totalFinal'] ?? null, NAN);
        if (! is_finite($biaya)) {
            $biaya = self::toPositiveNumber($pData['hargaPaket'] ?? 0, 0);
        }
        if (! $biaya && ! empty($pData['paket'])) {
            $nm = (string) $pData['paket'];
            if (str_contains($nm, '20')) {
                $biaya = 200000;
            } elseif (str_contains($nm, '30')) {
                $biaya = 250000;
            } elseif (str_contains($nm, '50')) {
                $biaya = 350000;
            } elseif (str_contains($nm, '100')) {
                $biaya = 500000;
            } else {
                $biaya = 150000;
            }
        }

        return $biaya ?: 0;
    }

    /** @param array<string,mixed> $pData */
    public static function diskonSnapshotFromPelanggan(array $pData): ?string
    {
        $raw = $pData['diskon'] ?? null;
        if (! is_array($raw)) {
            return null;
        }
        $keterangan = trim((string) ($raw['keterangan'] ?? ''));
        $nominal = self::toPositiveNumber($raw['nominal'] ?? 0, 0);
        if ($keterangan === '' && $nominal <= 0) {
            return null;
        }

        return json_encode(['keterangan' => $keterangan !== '' ? $keterangan : 'Diskon', 'nominal' => $nominal], JSON_UNESCAPED_UNICODE);
    }

    /** @param array<string,mixed> $pData */
    public static function biayaSnapshotFromPelanggan(array $pData): ?string
    {
        $items = [];
        foreach (['biayaTambahan1', 'biayaTambahan2'] as $slot) {
            $raw = $pData[$slot] ?? null;
            if (! is_array($raw)) {
                continue;
            }
            $rincian = trim((string) ($raw['rincian'] ?? ''));
            $nominal = self::toPositiveNumber($raw['nominal'] ?? 0, 0);
            if ($rincian === '' && $nominal <= 0) {
                continue;
            }
            $items[] = [
                'rincian' => $rincian !== '' ? $rincian : ($slot === 'biayaTambahan1' ? 'Biaya tambahan 1' : 'Biaya tambahan 2'),
                'nominal' => $nominal,
            ];
        }
        if ($items === []) {
            return null;
        }

        return json_encode(['items' => $items], JSON_UNESCAPED_UNICODE);
    }
}
