<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PublicChatController extends Controller
{
    /**
     * Get chat thread and messages for a customer
     */
    public function thread(Request $request)
    {
        $idPelanggan = $request->query('idPelanggan');
        
        if (!$idPelanggan) {
            return response()->json(['success' => false, 'error' => 'idPelanggan diperlukan'], 400);
        }

        $thread = DB::table('chat_threads')->where('idPelanggan', $idPelanggan)->first();

        if (!$thread) {
            return response()->json([
                'success' => true, 
                'data' => ['messages' => []]
            ]);
        }

        $messages = DB::table('chat_messages as m')
            ->leftJoin('users as u', 'u.id', '=', 'm.senderUserId')
            ->where('m.threadId', $thread->id)
            ->orderBy('m.createdAt')
            ->select('m.*', 'u.nama as senderName')
            ->get()
            ->map(function ($m) {
                return [
                    'id' => $m->id,
                    'fromCustomer' => $m->senderType === 'pelanggan',
                    'fromLabel' => $m->senderType === 'pelanggan' ? 'Anda' : ($m->senderName ?? 'Kantor'),
                    'body' => $m->body,
                    'createdAt' => $m->createdAt
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'messages' => $messages
            ]
        ]);
    }

    /**
     * Post a new message from the customer
     */
    public function postMessage(Request $request)
    {
        $idPelanggan = $request->input('idPelanggan');
        $body = trim((string) $request->input('body', ''));

        if (!$idPelanggan || $body === '') {
            return response()->json(['success' => false, 'error' => 'idPelanggan dan body diperlukan'], 400);
        }

        // Get Pelanggan Db Id
        $pelanggan = DB::table('pelanggan')->where('idPelanggan', $idPelanggan)->first();
        if (!$pelanggan) {
            return response()->json(['success' => false, 'error' => 'Pelanggan tidak ditemukan'], 404);
        }

        // Find or create thread
        $thread = DB::table('chat_threads')->where('idPelanggan', $idPelanggan)->first();
        $threadId = $thread ? $thread->id : null;

        if (!$thread) {
            $threadId = (string) Str::uuid();
            DB::table('chat_threads')->insert([
                'id' => $threadId,
                'idPelanggan' => $idPelanggan,
                'pelangganDbId' => $pelanggan->id,
                'lastMessageAt' => now()->format('Y-m-d H:i:s'),
                'createdAt' => now()->format('Y-m-d H:i:s'),
                'updatedAt' => now()->format('Y-m-d H:i:s')
            ]);
        } else {
            DB::table('chat_threads')->where('id', $threadId)->update([
                'lastMessageAt' => now()->format('Y-m-d H:i:s'),
                'updatedAt' => now()->format('Y-m-d H:i:s')
            ]);
        }

        // Insert message
        DB::table('chat_messages')->insert([
            'id' => (string) Str::uuid(),
            'threadId' => $threadId,
            'senderType' => 'pelanggan',
            'senderUserId' => $pelanggan->id,
            'body' => $body,
            'createdAt' => now()->format('Y-m-d H:i:s')
        ]);

        return response()->json(['success' => true]);
    }
}
