<?php

namespace App\Http\Controllers\Api\Owner;

use App\Http\Controllers\Controller;
use App\Services\StaffAuthService;
use App\Support\RoleHelper;
use App\Support\RowSerializer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ChatStaffController extends Controller
{
    public function __construct(private StaffAuthService $staffAuth) {}

    private function canUseChat(array $jwt): bool
    {
        if (RoleHelper::hasPermission($jwt, 'access_admin_app')) {
            return true;
        }
        $rk = RoleHelper::resolveRoleKey($jwt['roleKey'] ?? $jwt['role'] ?? '');

        return in_array($rk, ['teknisi', 'penagih', 'tekpen'], true);
    }

    /** @return array<string,mixed> */
    private function threadToArray(object $row): array
    {
        $t = RowSerializer::deserializeRow((array) $row) ?? [];
        $dj = $t['delegatedToJson'] ?? [];
        if (is_string($dj)) {
            $delegated = json_decode($dj, true) ?: [];
        } else {
            $delegated = is_array($dj) ? $dj : [];
        }
        $t['delegatedTo'] = $delegated;
        unset($t['delegatedToJson']);

        return $t;
    }

    public function threads(Request $request)
    {
        $jwt = $request->attributes->get('jwt_user');
        if (! $this->canUseChat($jwt)) {
            return response()->json(['error' => 'Akses ditolak'], 403);
        }
        $rows = DB::table('chat_threads as t')
            ->leftJoin('pelanggan as p', 'p.idPelanggan', '=', 't.idPelanggan')
            ->select('t.*', 'p.nama as namaPelanggan')
            ->orderByDesc('t.lastMessageAt')
            ->orderByDesc('t.updatedAt')
            ->limit(500)
            ->get()
            ->map(fn ($r) => $this->threadToArray($r))
            ->all();

        return response()->json(['success' => true, 'data' => $rows]);
    }

    public function show(Request $request, string $id)
    {
        $jwt = $request->attributes->get('jwt_user');
        if (! $this->canUseChat($jwt)) {
            return response()->json(['error' => 'Akses ditolak'], 403);
        }
        $row = DB::table('chat_threads as t')
            ->leftJoin('pelanggan as p', 'p.idPelanggan', '=', 't.idPelanggan')
            ->select('t.*', 'p.nama as namaPelanggan')
            ->where('t.id', $id)
            ->first();
        if (! $row) {
            return response()->json(['error' => 'Thread tidak ditemukan'], 404);
        }
        $thread = $this->threadToArray($row);
        $assignedName = null;
        if (! empty($thread['assignedUserId'])) {
            $u = DB::table('users')->select('nama')->where('id', $thread['assignedUserId'])->first();
            $assignedName = $u->nama ?? null;
        }
        $participants = DB::table('chat_staff_participants as c')
            ->join('users as u', 'u.id', '=', 'c.userId')
            ->where('c.threadId', $id)
            ->select('u.id as userId', 'u.nama')
            ->get()
            ->map(fn ($p) => ['userId' => $p->userId, 'nama' => $p->nama])
            ->all();
        $delegatedNames = [];
        foreach ($thread['delegatedTo'] ?? [] as $uid) {
            $u = DB::table('users')->select('nama')->where('id', $uid)->first();
            if ($u) {
                $delegatedNames[] = ['nama' => $u->nama];
            }
        }

        return response()->json([
            'data' => [
                'thread' => $thread,
                'assignedName' => $assignedName,
                'participants' => $participants,
                'delegated' => $delegatedNames,
            ],
        ]);
    }

    public function messages(Request $request, string $id)
    {
        $jwt = $request->attributes->get('jwt_user');
        if (! $this->canUseChat($jwt)) {
            return response()->json(['error' => 'Akses ditolak'], 403);
        }
        if (! DB::table('chat_threads')->where('id', $id)->exists()) {
            return response()->json(['error' => 'Thread tidak ditemukan'], 404);
        }
        $msgs = DB::table('chat_messages as m')
            ->leftJoin('users as u', 'u.id', '=', 'm.senderUserId')
            ->where('m.threadId', $id)
            ->orderBy('m.createdAt')
            ->select('m.*', 'u.nama as senderName')
            ->get()
            ->map(function ($m) {
                $a = (array) $m;

                return [
                    'id' => $a['id'],
                    'threadId' => $a['threadId'],
                    'senderType' => $a['senderType'],
                    'senderUserId' => $a['senderUserId'],
                    'senderName' => $a['senderName'] ?? null,
                    'body' => $a['body'],
                    'createdAt' => $a['createdAt'],
                ];
            })
            ->all();

        return response()->json(['success' => true, 'data' => $msgs]);
    }

    public function postMessage(Request $request, string $id)
    {
        $jwt = $request->attributes->get('jwt_user');
        if (! $this->canUseChat($jwt)) {
            return response()->json(['error' => 'Akses ditolak'], 403);
        }
        $thread = DB::table('chat_threads')->where('id', $id)->first();
        if (! $thread) {
            return response()->json(['error' => 'Thread tidak ditemukan'], 404);
        }
        $body = trim((string) $request->input('body', ''));
        if ($body === '') {
            return response()->json(['error' => 'Pesan kosong'], 400);
        }
        $uid = (string) ($jwt['uid'] ?? '');
        $ack = filter_var($request->input('acknowledgeSecondParticipant'), FILTER_VALIDATE_BOOLEAN);
        $assignee = (string) ($thread->assignedUserId ?? '');
        if ($assignee !== '' && $assignee !== $uid && ! $ack) {
            $isParticipant = DB::table('chat_staff_participants')
                ->where('threadId', $id)
                ->where('userId', $uid)
                ->exists();
            if (! $isParticipant) {
                return response()->json(['error' => 'SECOND_PARTICIPANT'], 409);
            }
        }
        if ($ack && $assignee !== '' && $assignee !== $uid) {
            if (! DB::table('chat_staff_participants')->where('threadId', $id)->where('userId', $uid)->exists()) {
                DB::table('chat_staff_participants')->insert([
                    'threadId' => $id,
                    'userId' => $uid,
                    'firstReplyAt' => now(),
                ]);
            }
        }
        DB::table('chat_messages')->insert([
            'id' => (string) Str::uuid(),
            'threadId' => $id,
            'senderType' => 'staff',
            'senderUserId' => $uid,
            'body' => $body,
            'createdAt' => now(),
        ]);
        DB::table('chat_threads')->where('id', $id)->update([
            'lastMessageAt' => now()->format('Y-m-d H:i:s'),
        ]);

        return response()->json(['success' => true]);
    }

    public function claim(Request $request, string $id)
    {
        $jwt = $request->attributes->get('jwt_user');
        if (! $this->canUseChat($jwt)) {
            return response()->json(['error' => 'Akses ditolak'], 403);
        }
        $uid = (string) ($jwt['uid'] ?? '');
        DB::table('chat_threads')->where('id', $id)->update([
            'assignedUserId' => $uid,
            'assignedAt' => now()->format('Y-m-d H:i:s'),
        ]);

        return response()->json(['success' => true]);
    }

    public function release(Request $request, string $id)
    {
        $jwt = $request->attributes->get('jwt_user');
        if (! $this->canUseChat($jwt)) {
            return response()->json(['error' => 'Akses ditolak'], 403);
        }
        $uid = (string) ($jwt['uid'] ?? '');
        $t = DB::table('chat_threads')->where('id', $id)->first();
        if ($t && (string) ($t->assignedUserId ?? '') === $uid) {
            DB::table('chat_threads')->where('id', $id)->update([
                'assignedUserId' => null,
                'assignedAt' => null,
            ]);
        }

        return response()->json(['success' => true]);
    }

    public function delegate(Request $request, string $id)
    {
        $jwt = $request->attributes->get('jwt_user');
        if (! $this->canUseChat($jwt)) {
            return response()->json(['error' => 'Akses ditolak'], 403);
        }
        $userIds = $request->input('userIds');
        if (! is_array($userIds)) {
            return response()->json(['error' => 'userIds wajib array'], 400);
        }
        $thread = DB::table('chat_threads')->where('id', $id)->first();
        if (! $thread) {
            return response()->json(['error' => 'Thread tidak ditemukan'], 404);
        }
        $cur = json_decode((string) ($thread->delegatedToJson ?? '[]'), true) ?: [];
        if (! is_array($cur)) {
            $cur = [];
        }
        $merged = array_values(array_unique([...$cur, ...array_map('strval', $userIds)]));
        DB::table('chat_threads')->where('id', $id)->update([
            'delegatedToJson' => json_encode($merged, JSON_UNESCAPED_UNICODE),
        ]);

        return response()->json(['success' => true]);
    }

    public function fieldPublic(Request $request, string $id)
    {
        $jwt = $request->attributes->get('jwt_user');
        if (! RoleHelper::hasPermission($jwt, 'access_admin_app')) {
            return response()->json(['error' => 'Akses ditolak'], 403);
        }
        $until = $request->input('until');
        $canReply = filter_var($request->input('canReply'), FILTER_VALIDATE_BOOLEAN);
        $untilSql = null;
        if ($until !== null && $until !== '') {
            $untilSql = RowSerializer::coerceMysqlDateTime((string) $until);
        }
        DB::table('chat_threads')->where('id', $id)->update([
            'fieldPublicUntil' => $untilSql,
            'fieldPublicCanReply' => $canReply ? 1 : 0,
        ]);

        return response()->json(['success' => true]);
    }

    public function officeUsers(Request $request)
    {
        $jwt = $request->attributes->get('jwt_user');
        if (! $this->canUseChat($jwt)) {
            return response()->json(['error' => 'Akses ditolak'], 403);
        }
        $out = [];
        foreach (DB::table('users')->where('aktif', 1)->orderBy('nama')->get() as $u) {
            $p = $this->staffAuth->profileFromRow((array) $u);
            if (! RoleHelper::hasPermission($p, 'access_admin_app')) {
                continue;
            }
            $out[] = ['id' => $p['id'], 'nama' => $p['nama'], 'email' => $p['email']];
        }

        return response()->json(['success' => true, 'data' => $out]);
    }
}
