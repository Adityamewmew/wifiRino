<?php

namespace App\Support;

use Carbon\Carbon;

class RowSerializer
{
    /**
     * MySQL timestamp/datetime columns reject ISO-8601 (e.g. ...T...Z). Coerce only when the string is clearly ISO-like.
     */
    public static function coerceMysqlDateTime(mixed $v): mixed
    {
        if (! is_string($v) || $v === '') {
            return $v;
        }
        // YYYY-MM-DD saja — biarkan (dipakai varchar seperti mulaiTagihan; MySQL juga menerima untuk date/datetime)
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $v)) {
            return $v;
        }
        $isIsoLike = str_contains($v, 'T') || str_contains($v, 'Z')
            || (bool) preg_match('/[+-]\d{2}:?\d{2}$/', $v);
        if (! $isIsoLike) {
            return $v;
        }
        if (! preg_match('/^\d{4}-\d{2}-\d{2}/', $v)) {
            return $v;
        }
        try {
            return Carbon::parse($v)->format('Y-m-d H:i:s');
        } catch (\Throwable) {
            return $v;
        }
    }

    /** @param array<string,mixed> $row */
    public static function deserializeRow(?array $row): ?array
    {
        if ($row === null) {
            return null;
        }
        $out = [];
        foreach ($row as $k => $v) {
            if (is_string($v) && ($v !== '') && ($v[0] === '{' || $v[0] === '[')) {
                try {
                    $out[$k] = json_decode($v, true, 512, JSON_THROW_ON_ERROR);
                } catch (\Throwable) {
                    $out[$k] = $v;
                }
            } else {
                $out[$k] = $v;
            }
        }

        return $out;
    }

    /** @param array<string,mixed> $data */
    public static function serializeForDb(array $data): array
    {
        $out = [];
        foreach ($data as $k => $v) {
            if ($v !== null && is_array($v)) {
                $out[$k] = json_encode($v, JSON_UNESCAPED_UNICODE);
            } else {
                $out[$k] = self::coerceMysqlDateTime($v);
            }
        }

        return $out;
    }
}
