<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Support\RoleHelper;
use App\Support\RowSerializer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PengumumanPublicController extends Controller
{
    public function aktif(Request $request)
    {
        $idPelanggan = strtolower(trim((string) ($request->query('idPelanggan', $request->query('id', '')))));
        $area = strtolower(trim((string) $request->query('area', '')));
        $nowMs = (int) (microtime(true) * 1000);

        $rows = DB::table('pengumuman')->whereRaw('COALESCE(aktif,1) = 1')->orderByDesc('createdAt')->get();
        $areaRows = DB::table('areas')->select('id', 'nama')->get();
        $areaById = [];
        foreach ($areaRows as $a) {
            $areaById[strtolower(trim((string) $a->id))] = strtolower(trim((string) $a->nama));
        }

        $filtered = [];
        foreach ($rows as $row) {
            $item = RowSerializer::deserializeRow((array) $row) ?? [];
            $startMs = ! empty($item['startAt']) ? strtotime((string) $item['startAt']) * 1000 : null;
            $endMs = ! empty($item['endAt']) ? strtotime((string) $item['endAt']) * 1000 : null;
            if (is_int($startMs) && $nowMs < $startMs) {
                continue;
            }
            if (is_int($endMs) && $nowMs > $endMs) {
                continue;
            }
            $targetType = RoleHelper::normText($item['targetType'] ?? 'global') ?: 'global';
            if ($targetType === 'global') {
                $filtered[] = $item;

                continue;
            }
            if ($targetType === 'area') {
                if ($area === '') {
                    continue;
                }
                $areaId = RoleHelper::normText($item['targetAreaId'] ?? '');
                $areaName = RoleHelper::normText($item['targetAreaName'] ?? '');
                $nameFromId = $areaById[$areaId] ?? '';
                if ($area === $areaName || $area === $nameFromId) {
                    $filtered[] = $item;
                }

                continue;
            }
            if ($targetType === 'pelanggan') {
                if ($idPelanggan === '') {
                    continue;
                }
                $ids = $this->parseJsonArray($item['targetPelangganIds'] ?? null);
                $ids = array_map(fn ($v) => strtolower(trim((string) $v)), $ids);
                $ids = array_filter($ids);
                if (in_array($idPelanggan, $ids, true)) {
                    $filtered[] = $item;
                }
            }
        }

        $data = array_map(function ($item) {
            return [
                'id' => $item['id'] ?? null,
                'targetType' => $item['targetType'] ?? 'global',
                'targetAreaId' => $item['targetAreaId'] ?? '',
                'targetAreaName' => $item['targetAreaName'] ?? '',
                'targetPelangganIds' => $this->parseJsonArray($item['targetPelangganIds'] ?? null),
                'pesan' => $item['pesan'] ?? '',
                'startAt' => $item['startAt'] ?? null,
                'endAt' => $item['endAt'] ?? null,
                'createdBy' => $item['createdBy'] ?? '',
                'createdAt' => $item['createdAt'] ?? null,
            ];
        }, $filtered);

        return response()->json(['success' => true, 'count' => count($data), 'data' => $data]);
    }

    /** @return list<string> */
    private function parseJsonArray(mixed $raw): array
    {
        if (is_array($raw)) {
            return array_values($raw);
        }
        if (! is_string($raw) || trim($raw) === '') {
            return [];
        }
        try {
            $j = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
            if (! is_array($j)) {
                return [];
            }

            return array_values(array_map('strval', $j));
        } catch (\Throwable) {
            return [];
        }
    }
}
