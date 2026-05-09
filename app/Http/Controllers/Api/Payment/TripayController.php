<?php

namespace App\Http\Controllers\Api\Payment;

use App\Http\Controllers\Controller;
use App\Services\TripayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TripayController extends Controller
{
    public function __construct(private TripayService $tripay) {}

    /**
     * GET /api/payment/channels
     * List available payment channels.
     */
    public function channels()
    {
        if (! $this->tripay->isConfigured()) {
            return response()->json([
                'success' => false,
                'message' => 'Tripay belum dikonfigurasi. Hubungi administrator.',
            ], 503);
        }

        $channels = $this->tripay->getPaymentChannels();

        // Group by category
        $grouped = collect($channels)->filter(fn ($ch) => $ch['active'] ?? false)->groupBy('group')->toArray();

        return response()->json(['success' => true, 'data' => $grouped]);
    }

    /**
     * POST /api/payment/create-invoice
     * Create a Tripay invoice for a tagihan_bulanan record.
     *
     * Body: { tagihanId: string, method: string }
     */
    public function createInvoice(Request $request)
    {
        if (! $this->tripay->isConfigured()) {
            return response()->json([
                'success' => false,
                'message' => 'Tripay belum dikonfigurasi.',
            ], 503);
        }

        $tagihanId = $request->input('tagihanId');
        $method = $request->input('method'); // e.g. 'BRIVA', 'QRIS', 'BCAVA'

        if (! $tagihanId || ! $method) {
            return response()->json([
                'success' => false,
                'message' => 'tagihanId dan method wajib diisi.',
            ], 400);
        }

        // Fetch tagihan
        $tagihan = DB::table('tagihan_bulanan')->where('id', $tagihanId)->first();
        if (! $tagihan) {
            return response()->json([
                'success' => false,
                'message' => 'Tagihan tidak ditemukan.',
            ], 404);
        }

        if ($tagihan->status === 'lunas') {
            return response()->json([
                'success' => false,
                'message' => 'Tagihan ini sudah lunas.',
            ], 400);
        }

        // Check if there's already an active Tripay transaction
        if (! empty($tagihan->tripay_reference)) {
            $existing = $this->tripay->getTransactionDetail($tagihan->tripay_reference);
            if ($existing && ($existing['status'] ?? '') === 'UNPAID') {
                return response()->json([
                    'success' => true,
                    'message' => 'Invoice sudah dibuat sebelumnya.',
                    'data' => [
                        'reference' => $existing['reference'],
                        'checkout_url' => $existing['checkout_url'],
                        'pay_code' => $existing['pay_code'] ?? null,
                        'pay_url' => $existing['pay_url'] ?? null,
                        'qr_url' => $existing['qr_url'] ?? null,
                        'qr_string' => $existing['qr_string'] ?? null,
                        'amount' => $existing['amount'],
                        'expired_time' => $existing['expired_time'],
                        'payment_name' => $existing['payment_name'],
                        'instructions' => $existing['instructions'] ?? [],
                    ],
                ]);
            }
        }

        // Build merchant reference: TAG-{idPelanggan}-{bulan}{tahun}-{short_uuid}
        $merchantRef = sprintf(
            'TAG-%s-%02d%d-%s',
            $tagihan->idPelanggan ?? 'X',
            $tagihan->bulan ?? 0,
            $tagihan->tahun ?? 0,
            strtoupper(substr(Str::uuid(), 0, 8))
        );

        $amount = (int) round((float) ($tagihan->totalTagihan ?? 0));
        if ($amount <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Nominal tagihan tidak valid.',
            ], 400);
        }

        // Lookup pelanggan for additional info
        $pelanggan = DB::table('pelanggan')
            ->where('idPelanggan', $tagihan->idPelanggan)
            ->first();

        $namaBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];
        $periodeLabel = ($namaBulan[$tagihan->bulan] ?? $tagihan->bulan) . ' ' . $tagihan->tahun;

        $result = $this->tripay->createTransaction([
            'method' => $method,
            'merchant_ref' => $merchantRef,
            'amount' => $amount,
            'customer_name' => $tagihan->namaPelanggan ?? $pelanggan->nama ?? 'Pelanggan',
            'customer_email' => $pelanggan->email ?? null,
            'customer_phone' => $tagihan->noWA ?? $pelanggan->noWA ?? null,
            'order_items' => [
                [
                    'sku' => $tagihan->idPelanggan ?? 'INET',
                    'name' => 'Tagihan Internet ' . $periodeLabel,
                    'price' => $amount,
                    'quantity' => 1,
                ],
            ],
        ]);

        if (! ($result['success'] ?? false)) {
            return response()->json([
                'success' => false,
                'message' => $result['message'] ?? 'Gagal membuat invoice.',
            ], 500);
        }

        $txData = $result['data'];

        // Save Tripay reference to tagihan
        DB::table('tagihan_bulanan')->where('id', $tagihanId)->update([
            'tripay_reference' => $txData['reference'] ?? null,
            'tripay_checkout_url' => $txData['checkout_url'] ?? null,
            'tripay_pay_code' => $txData['pay_code'] ?? null,
            'tripay_method' => $txData['payment_method'] ?? $method,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Invoice berhasil dibuat.',
            'data' => [
                'reference' => $txData['reference'],
                'checkout_url' => $txData['checkout_url'],
                'pay_code' => $txData['pay_code'] ?? null,
                'pay_url' => $txData['pay_url'] ?? null,
                'qr_url' => $txData['qr_url'] ?? null,
                'qr_string' => $txData['qr_string'] ?? null,
                'amount' => $txData['amount'],
                'fee_customer' => $txData['fee_customer'] ?? 0,
                'total_fee' => $txData['total_fee'] ?? 0,
                'expired_time' => $txData['expired_time'],
                'payment_name' => $txData['payment_name'],
                'instructions' => $txData['instructions'] ?? [],
            ],
        ]);
    }

    /**
     * POST /api/payment/callback
     * Webhook handler — called by Tripay when payment status changes.
     * No authentication middleware — verified by HMAC signature.
     */
    public function callback(Request $request)
    {
        $rawBody = $request->getContent();
        $callbackSignature = $request->header('X-Callback-Signature', '');

        // Validate signature
        if (! $this->tripay->validateCallbackSignature($rawBody, $callbackSignature)) {
            Log::warning('Tripay callback: invalid signature', [
                'ip' => $request->ip(),
            ]);
            return response()->json(['success' => false, 'message' => 'Invalid signature'], 403);
        }

        $payload = json_decode($rawBody, true);
        if (! $payload) {
            return response()->json(['success' => false, 'message' => 'Invalid JSON'], 400);
        }

        $merchantRef = $payload['merchant_ref'] ?? '';
        $reference = $payload['reference'] ?? '';
        $status = $payload['status'] ?? '';

        Log::info('Tripay callback received', [
            'reference' => $reference,
            'merchant_ref' => $merchantRef,
            'status' => $status,
        ]);

        // Only process PAID status
        if ($status !== 'PAID') {
            // For EXPIRED/FAILED, we could clear the tripay_reference
            if (in_array($status, ['EXPIRED', 'FAILED'])) {
                DB::table('tagihan_bulanan')
                    ->where('tripay_reference', $reference)
                    ->whereNot('status', 'lunas')
                    ->update([
                        'tripay_reference' => null,
                        'tripay_checkout_url' => null,
                        'tripay_pay_code' => null,
                        'tripay_method' => null,
                    ]);
            }

            return response()->json(['success' => true]);
        }

        // Find tagihan by tripay_reference
        $tagihan = DB::table('tagihan_bulanan')
            ->where('tripay_reference', $reference)
            ->first();

        if (! $tagihan) {
            Log::warning('Tripay callback: tagihan not found', ['reference' => $reference]);
            return response()->json(['success' => false, 'message' => 'Tagihan not found'], 404);
        }

        if ($tagihan->status === 'lunas') {
            // Already paid, ignore duplicate
            return response()->json(['success' => true]);
        }

        $paymentMethod = $payload['payment_method'] ?? $tagihan->tripay_method ?? 'online';
        $paymentName = $payload['payment_name'] ?? $paymentMethod;

        // Update tagihan → lunas
        DB::table('tagihan_bulanan')->where('id', $tagihan->id)->update([
            'status' => 'lunas',
            'tglBayar' => now()->format('Y-m-d H:i:s'),
            'metodeBayar' => 'tripay_' . strtolower($paymentMethod),
            'dibayar_ke' => 'tripay:' . $reference,
        ]);

        // Auto-record pembukuan (accounting entry)
        $amount = (int) round((float) ($tagihan->totalTagihan ?? 0));
        $namaBulan = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
            5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Ags',
            9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des',
        ];
        $periodeShort = ($namaBulan[$tagihan->bulan] ?? $tagihan->bulan) . ' ' . $tagihan->tahun;

        DB::table('pembukuan')->insert([
            'id' => (string) Str::uuid(),
            'tanggal' => now()->format('Y-m-d H:i:s'),
            'jenis' => 'pemasukan',
            'kategori' => 'Tagihan Internet',
            'nominal' => $amount,
            'keterangan' => sprintf(
                'Pembayaran online (%s) - %s (%s) - %s',
                $paymentName,
                $tagihan->namaPelanggan ?? '',
                $tagihan->idPelanggan ?? '',
                $periodeShort
            ),
            'idReferensi' => $tagihan->id,
            'createdBy' => 'system:tripay',
            'createdAt' => now()->format('Y-m-d H:i:s'),
        ]);

        // Log audit
        DB::table('audit_logs')->insert([
            'id' => (string) Str::uuid(),
            'userId' => 'system:tripay',
            'action' => 'bayar_online',
            'entity' => 'tagihan_bulanan',
            'entityId' => $tagihan->id,
            'details' => json_encode([
                'reference' => $reference,
                'method' => $paymentMethod,
                'amount' => $amount,
                'pelanggan' => $tagihan->idPelanggan,
            ]),
            'createdAt' => now()->format('Y-m-d H:i:s'),
        ]);

        Log::info('Tripay payment processed', [
            'tagihan_id' => $tagihan->id,
            'pelanggan' => $tagihan->idPelanggan,
            'amount' => $amount,
            'method' => $paymentMethod,
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * GET /api/payment/status/{reference}
     * Check payment status by Tripay reference.
     */
    public function status(string $reference)
    {
        if (! $this->tripay->isConfigured()) {
            return response()->json([
                'success' => false,
                'message' => 'Tripay belum dikonfigurasi.',
            ], 503);
        }

        $detail = $this->tripay->getTransactionDetail($reference);
        if (! $detail) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'reference' => $detail['reference'],
                'status' => $detail['status'],
                'amount' => $detail['amount'],
                'payment_name' => $detail['payment_name'] ?? null,
                'pay_code' => $detail['pay_code'] ?? null,
                'paid_at' => $detail['paid_at'] ?? null,
                'expired_time' => $detail['expired_time'] ?? null,
            ],
        ]);
    }
}
