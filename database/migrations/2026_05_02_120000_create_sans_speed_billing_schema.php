<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Skema MySQL menyamai SQLite billing (Node). Nama kolom camelCase dipertahankan
 * agar port API/frontend lebih mudah. Tipe disesuaikan untuk MySQL/InnoDB utf8mb4.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->char('id', 36)->primary();
            $table->string('nama', 255)->nullable();
            $table->string('email', 191)->unique();
            $table->string('password', 255)->nullable();
            $table->string('noWA', 64)->nullable();
            $table->string('role', 64)->nullable();
            $table->decimal('gaji', 15, 2)->nullable();
            $table->unsignedTinyInteger('aktif')->default(1);
            $table->longText('areas')->nullable();
            $table->timestamp('createdAt')->useCurrent();
        });

        Schema::create('areas', function (Blueprint $table) {
            $table->char('id', 36)->primary();
            $table->string('nama', 191)->unique();
            $table->text('keterangan')->nullable();
            $table->timestamp('createdAt')->useCurrent();
        });

        Schema::create('paket', function (Blueprint $table) {
            $table->char('id', 36)->primary();
            $table->string('nama', 191)->unique();
            $table->decimal('harga', 15, 2)->nullable();
            $table->text('deskripsi')->nullable();
            $table->unsignedTinyInteger('aktif')->default(1);
            $table->timestamp('createdAt')->useCurrent();
        });

        Schema::create('pelanggan', function (Blueprint $table) {
            $table->char('id', 36)->primary();
            $table->string('idPelanggan', 191)->nullable()->unique();
            $table->string('nama', 255)->nullable();
            $table->string('noWA', 64)->nullable();
            $table->string('area', 191)->nullable();
            $table->string('paket', 191)->nullable();
            $table->decimal('hargaPaket', 15, 2)->nullable();
            $table->integer('tglTagih')->nullable();
            $table->text('alamat')->nullable();
            $table->string('status', 64)->nullable();
            $table->string('idPPOE', 191)->nullable();
            $table->text('biayaTambahan1')->nullable();
            $table->text('biayaTambahan2')->nullable();
            $table->text('diskon')->nullable();
            $table->decimal('totalFinal', 15, 2)->nullable();
            $table->decimal('saldoDeposit', 15, 2)->default(0);
            $table->integer('bulanMulai')->nullable();
            $table->integer('tahunMulai')->nullable();
            $table->string('tanggalMulaiStr', 64)->nullable();
            $table->string('mulaiTagihan', 64)->nullable();
            $table->string('email', 191)->nullable();
            $table->string('noKtp', 64)->nullable();
            $table->text('foto1')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->text('keterangan')->nullable();
            $table->text('lamaBerlanggananTeks')->nullable();
            $table->timestamp('createdAt')->useCurrent();
            $table->timestamp('updatedAt')->useCurrent()->useCurrentOnUpdate();
        });

        Schema::create('mikrotik_routers', function (Blueprint $table) {
            $table->char('id', 36)->primary();
            $table->string('nama', 255);
            $table->string('host', 255)->nullable();
            $table->unsignedSmallInteger('apiPort')->default(8728);
            $table->string('apiUser', 191)->nullable();
            $table->text('apiPassword')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('rosVersi', 64)->nullable();
            $table->string('userManager', 191)->nullable();
            $table->string('hotspotManager', 191)->nullable();
            $table->string('serviceType', 64)->nullable();
            $table->dateTime('lastProbeAt')->nullable();
            $table->integer('lastProbeMs')->nullable();
            $table->unsignedTinyInteger('lastProbeOk')->nullable();
            $table->timestamp('createdAt')->useCurrent();
            $table->timestamp('updatedAt')->useCurrent()->useCurrentOnUpdate();
        });

        Schema::create('pelanggan_mikrotik', function (Blueprint $table) {
            $table->char('id', 36)->primary();
            $table->char('pelangganDbId', 36);
            $table->char('routerId', 36)->nullable();
            $table->string('profile', 191)->nullable();
            $table->string('ipPool', 191)->nullable();
            $table->string('isolirAddressList', 191)->default('isolir-billing');
            $table->string('simpleQueueName', 191)->nullable();
            $table->text('catatanTeknis')->nullable();
            $table->timestamp('createdAt')->useCurrent();
            $table->timestamp('updatedAt')->useCurrent()->useCurrentOnUpdate();

            $table->unique('pelangganDbId');
            $table->index('routerId', 'idx_pelanggan_mikrotik_router');
            $table->foreign('pelangganDbId')->references('id')->on('pelanggan')->cascadeOnDelete();
            $table->foreign('routerId')->references('id')->on('mikrotik_routers')->nullOnDelete();
        });

        Schema::create('tagihan_bulanan', function (Blueprint $table) {
            $table->char('id', 36)->primary();
            $table->string('idPelanggan', 191)->nullable()->index();
            $table->string('namaPelanggan', 255)->nullable();
            $table->string('area', 191)->nullable();
            $table->string('paket', 191)->nullable();
            $table->string('noWA', 64)->nullable();
            $table->unsignedTinyInteger('bulan')->nullable();
            $table->smallInteger('tahun')->nullable();
            $table->decimal('totalTagihan', 15, 2)->nullable();
            $table->string('status', 64)->nullable();
            $table->dateTime('tglJatuhTempo')->nullable();
            $table->dateTime('tglIsolir')->nullable();
            $table->text('diskonSnapshot')->nullable();
            $table->text('biayaSnapshot')->nullable();
            $table->dateTime('tglBayar')->nullable();
            $table->string('metodeBayar', 128)->nullable();
            $table->string('dibayar_ke', 191)->nullable();
            $table->string('dimukaBatchId', 64)->nullable();
            $table->timestamp('createdAt')->useCurrent();
        });

        Schema::create('pembukuan', function (Blueprint $table) {
            $table->char('id', 36)->primary();
            $table->dateTime('tanggal')->nullable();
            $table->string('jenis', 128)->nullable();
            $table->string('kategori', 191)->nullable();
            $table->decimal('nominal', 15, 2)->nullable();
            $table->text('keterangan')->nullable();
            $table->string('idReferensi', 191)->nullable();
            $table->string('createdBy', 36)->nullable();
            $table->timestamp('createdAt')->useCurrent();
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->char('id', 36)->primary();
            $table->timestamp('tanggal')->useCurrent();
            $table->string('userEmail', 191)->nullable();
            $table->string('userRole', 64)->nullable();
            $table->string('aksi', 191)->nullable();
            $table->string('entitas', 191)->nullable();
            $table->string('idData', 191)->nullable();
            $table->text('keterangan')->nullable();
        });

        Schema::create('pengaturan', function (Blueprint $table) {
            $table->string('kunci', 191)->primary();
            $table->longText('nilai')->nullable();
        });

        Schema::create('pengumuman', function (Blueprint $table) {
            $table->char('id', 36)->primary();
            $table->string('targetType', 32)->default('global');
            $table->string('targetAreaId', 36)->nullable();
            $table->string('targetAreaName', 191)->nullable();
            $table->text('targetPelangganIds')->nullable();
            $table->text('pesan')->nullable();
            $table->dateTime('startAt')->nullable();
            $table->dateTime('endAt')->nullable();
            $table->unsignedTinyInteger('aktif')->default(1);
            $table->string('createdBy', 36)->nullable();
            $table->timestamp('createdAt')->useCurrent();
        });

        Schema::create('push_tokens', function (Blueprint $table) {
            $table->char('id', 36)->primary();
            $table->string('token', 512)->unique();
            $table->string('targetType', 32)->default('global');
            $table->string('targetId', 191)->nullable();
            $table->string('roleKey', 64)->nullable();
            $table->string('platform', 64)->nullable();
            $table->text('deviceInfo')->nullable();
            $table->unsignedTinyInteger('isActive')->default(1);
            $table->dateTime('lastSeen')->nullable();
            $table->timestamp('createdAt')->useCurrent();
            $table->timestamp('updatedAt')->useCurrent()->useCurrentOnUpdate();
        });

        Schema::create('push_dispatch_logs', function (Blueprint $table) {
            $table->char('id', 36)->primary();
            $table->string('eventKey', 191)->nullable()->unique();
            $table->string('kategori', 128)->nullable();
            $table->string('targetType', 32)->nullable();
            $table->string('targetId', 191)->nullable();
            $table->string('refId', 191)->nullable();
            $table->string('title', 255)->nullable();
            $table->text('body')->nullable();
            $table->longText('payload')->nullable();
            $table->unsignedInteger('successCount')->default(0);
            $table->unsignedInteger('failedCount')->default(0);
            $table->timestamp('createdAt')->useCurrent();
        });

        Schema::create('pending_delete_requests', function (Blueprint $table) {
            $table->char('id', 36)->primary();
            $table->string('requestType', 64)->nullable();
            $table->string('targetId', 191)->nullable();
            $table->string('targetLabel', 255)->nullable();
            $table->text('reason')->nullable();
            $table->string('status', 32)->default('pending');
            $table->string('requestedByUid', 36)->nullable();
            $table->string('requestedByEmail', 191)->nullable();
            $table->string('requestedByRole', 64)->nullable();
            $table->string('approvedByUid', 36)->nullable();
            $table->string('approvedByEmail', 191)->nullable();
            $table->dateTime('approvedAt')->nullable();
            $table->timestamp('createdAt')->useCurrent();
            $table->timestamp('updatedAt')->useCurrent()->useCurrentOnUpdate();
        });

        Schema::create('tugas_teknisi', function (Blueprint $table) {
            $table->char('id', 36)->primary();
            $table->string('judul', 255)->nullable();
            $table->text('deskripsi')->nullable();
            $table->string('jenisTask', 128)->nullable();
            $table->string('prioritas', 32)->default('normal');
            $table->string('status', 32)->default('pending');
            $table->string('assignTo', 36)->nullable();
            $table->string('assignToNama', 191)->nullable();
            $table->unsignedTinyInteger('isBroadcast')->default(0);
            $table->string('claimedBy', 36)->nullable();
            $table->string('claimedByNama', 191)->nullable();
            $table->dateTime('claimedAt')->nullable();
            $table->string('idPelanggan', 191)->nullable();
            $table->string('namaPelanggan', 255)->nullable();
            $table->text('alamat')->nullable();
            $table->string('noWA', 64)->nullable();
            $table->dateTime('tglDibuat')->nullable();
            $table->dateTime('tglDeadline')->nullable();
            $table->dateTime('tglSelesai')->nullable();
            $table->text('catatanTeknisi')->nullable();
            $table->string('createdBy', 36)->nullable();
            $table->timestamp('createdAt')->useCurrent();
        });

        Schema::create('chat_threads', function (Blueprint $table) {
            $table->char('id', 36)->primary();
            $table->string('idPelanggan', 191)->unique();
            $table->char('pelangganDbId', 36)->nullable();
            $table->string('assignedUserId', 36)->nullable();
            $table->dateTime('assignedAt')->nullable();
            $table->dateTime('lastMessageAt')->nullable()->index('idx_chat_threads_last');
            $table->dateTime('fieldPublicUntil')->nullable();
            $table->unsignedTinyInteger('fieldPublicCanReply')->default(0);
            $table->json('delegatedToJson')->nullable();
            $table->timestamp('createdAt')->useCurrent();
            $table->timestamp('updatedAt')->useCurrent()->useCurrentOnUpdate();
        });

        Schema::create('chat_messages', function (Blueprint $table) {
            $table->char('id', 36)->primary();
            $table->char('threadId', 36);
            $table->string('senderType', 32);
            $table->string('senderUserId', 36)->nullable();
            $table->text('body');
            $table->timestamp('createdAt')->useCurrent();

            $table->index('threadId', 'idx_chat_messages_thread');
            $table->foreign('threadId')->references('id')->on('chat_threads')->cascadeOnDelete();
        });

        Schema::create('chat_staff_participants', function (Blueprint $table) {
            $table->char('threadId', 36);
            $table->char('userId', 36);
            $table->timestamp('firstReplyAt')->useCurrent();

            $table->primary(['threadId', 'userId']);
            $table->foreign('threadId')->references('id')->on('chat_threads')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_staff_participants');
        Schema::dropIfExists('chat_messages');
        Schema::dropIfExists('chat_threads');
        Schema::dropIfExists('tugas_teknisi');
        Schema::dropIfExists('pending_delete_requests');
        Schema::dropIfExists('push_dispatch_logs');
        Schema::dropIfExists('push_tokens');
        Schema::dropIfExists('pengumuman');
        Schema::dropIfExists('pengaturan');
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('pembukuan');
        Schema::dropIfExists('tagihan_bulanan');
        Schema::dropIfExists('pelanggan_mikrotik');
        Schema::dropIfExists('mikrotik_routers');
        Schema::dropIfExists('pelanggan');
        Schema::dropIfExists('paket');
        Schema::dropIfExists('areas');
        Schema::dropIfExists('users');
    }
};
