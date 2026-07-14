<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PickupProof;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()->where('role', 'admin')->firstOrFail();
        $urea = Product::query()->where('slug', 'pupuk-urea-50kg')->firstOrFail();
        $cabai = Product::query()->where('slug', 'benih-cabai-f1')->firstOrFail();

        $transactions = [
            ['code' => 'TRX-001', 'customer' => 'Andi Wijaya', 'total' => 197500, 'date' => '07/07/2026', 'payment' => 'QRIS', 'payment_status' => 'lunas', 'pickup_status' => 'menunggu_approval'],
            ['code' => 'TRX-002', 'customer' => 'Siti Rahayu', 'total' => 85000, 'date' => '07/07/2026', 'payment' => 'VA BCA', 'payment_status' => 'lunas', 'pickup_status' => 'disetujui'],
            ['code' => 'TRX-003', 'customer' => 'Budi Santoso', 'total' => 125000, 'date' => '06/07/2026', 'payment' => 'QRIS', 'payment_status' => 'menunggu_pembayaran', 'pickup_status' => null],
            ['code' => 'TRX-004', 'customer' => 'Dewi Lestari', 'total' => 45000, 'date' => '06/07/2026', 'payment' => 'VA Mandiri', 'payment_status' => 'lunas', 'pickup_status' => 'selesai'],
            ['code' => 'TRX-005', 'customer' => 'Rudi Hartono', 'total' => 95000, 'date' => '06/07/2026', 'payment' => 'QRIS', 'payment_status' => 'lunas', 'pickup_status' => 'menunggu_approval'],
            ['code' => 'TRX-006', 'customer' => 'Maya Sari', 'total' => 150000, 'date' => '05/07/2026', 'payment' => 'VA BNI', 'payment_status' => 'lunas', 'pickup_status' => 'selesai'],
            ['code' => 'TRX-007', 'customer' => 'Joko Susilo', 'total' => 35000, 'date' => '05/07/2026', 'payment' => 'QRIS', 'payment_status' => 'expired', 'pickup_status' => null],
            ['code' => 'TRX-008', 'customer' => 'Ani Wulandari', 'total' => 78000, 'date' => '05/07/2026', 'payment' => 'VA BCA', 'payment_status' => 'lunas', 'pickup_status' => 'ditolak'],
            ['code' => 'TRX-009', 'customer' => 'Hendra Kusuma', 'total' => 220000, 'date' => '04/07/2026', 'payment' => 'QRIS', 'payment_status' => 'lunas', 'pickup_status' => 'menunggu_approval'],
            ['code' => 'TRX-010', 'customer' => 'Fitri Anggraini', 'total' => 65000, 'date' => '04/07/2026', 'payment' => 'VA Mandiri', 'payment_status' => 'lunas', 'pickup_status' => 'disetujui'],
            ['code' => 'TRX-011', 'customer' => 'Agus Prasetyo', 'total' => 180000, 'date' => '04/07/2026', 'payment' => 'QRIS', 'payment_status' => 'menunggu_pembayaran', 'pickup_status' => null],
            ['code' => 'TRX-012', 'customer' => 'Rina Melati', 'total' => 42000, 'date' => '03/07/2026', 'payment' => 'VA BCA', 'payment_status' => 'lunas', 'pickup_status' => 'selesai'],
            ['code' => 'TRX-013', 'customer' => 'Bambang Wijaya', 'total' => 88000, 'date' => '03/07/2026', 'payment' => 'QRIS', 'payment_status' => 'lunas', 'pickup_status' => 'menunggu_approval'],
            ['code' => 'TRX-014', 'customer' => 'Sri Mulyani', 'total' => 32000, 'date' => '02/07/2026', 'payment' => 'VA BNI', 'payment_status' => 'lunas', 'pickup_status' => 'selesai'],
            ['code' => 'TRX-015', 'customer' => 'Eko Prasetya', 'total' => 175000, 'date' => '02/07/2026', 'payment' => 'QRIS', 'payment_status' => 'lunas', 'pickup_status' => 'disetujui'],
        ];

        $proofs = [
            'TRX-002' => ['verified_at' => '07/07/2026 10:32', 'note' => 'Barang diambil langsung di toko'],
            'TRX-004' => ['verified_at' => '06/07/2026 14:15', 'note' => 'Pelanggan bawa KTP, barang lengkap'],
            'TRX-006' => ['verified_at' => '05/07/2026 09:48', 'note' => 'Diambil oleh keluarga pembeli'],
            'TRX-010' => ['verified_at' => '04/07/2026 16:20', 'note' => 'Foto barang & tanda tangan pelanggan'],
            'TRX-012' => ['verified_at' => '03/07/2026 11:05', 'note' => 'Pengambilan di counter depan'],
            'TRX-015' => ['verified_at' => '02/07/2026 13:40', 'note' => 'Barang sudah dicek kelengkapan'],
        ];

        foreach ($transactions as $trx) {
            $user = User::query()->where('name', $trx['customer'])->first();
            if (! $user) {
                continue;
            }

            $address = $user->addresses()->where('is_default', true)->first()
                ?? $user->addresses()->first();

            $createdAt = Carbon::createFromFormat('d/m/Y', $trx['date'])->startOfDay()->addHours(10);
            $paidAt = $trx['payment_status'] === 'lunas' ? $createdAt->copy()->addMinutes(15) : null;

            $order = Order::query()->updateOrCreate(
                ['code' => $trx['code']],
                [
                    'user_id' => $user->id,
                    'address_id' => $address?->id,
                    'buyer_name' => $user->name,
                    'buyer_phone' => $user->phone ?? '0813-0000-0000',
                    'subtotal' => $trx['total'] - 2500,
                    'admin_fee' => 2500,
                    'total' => $trx['total'],
                    'payment_status' => $trx['payment_status'],
                    'pickup_status' => $trx['pickup_status'],
                    'payment_method' => $this->mapPaymentMethod($trx['payment']),
                    'midtrans_order_id' => $trx['code'],
                    'paid_at' => $paidAt,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ],
            );

            if ($trx['code'] === 'TRX-001') {
                OrderItem::query()->where('order_id', $order->id)->delete();
                OrderItem::query()->create([
                    'order_id' => $order->id,
                    'product_id' => $urea->id,
                    'product_name' => $urea->name,
                    'unit_price' => $urea->price,
                    'quantity' => 2,
                    'subtotal' => $urea->price * 2,
                ]);
                OrderItem::query()->create([
                    'order_id' => $order->id,
                    'product_id' => $cabai->id,
                    'product_name' => $cabai->name,
                    'unit_price' => $cabai->price,
                    'quantity' => 1,
                    'subtotal' => $cabai->price,
                ]);
            }

            if (isset($proofs[$trx['code']])) {
                $proof = $proofs[$trx['code']];
                PickupProof::query()->updateOrCreate(
                    ['order_id' => $order->id],
                    [
                        'verified_by' => $admin->id,
                        'photo_path' => 'pickup-proofs/'.$trx['code'].'.jpg',
                        'note' => $proof['note'],
                        'verified_at' => Carbon::createFromFormat('d/m/Y H:i', $proof['verified_at']),
                    ],
                );
            }
        }
    }

    private function mapPaymentMethod(string $payment): string
    {
        return match ($payment) {
            'QRIS' => 'qris',
            'VA BCA' => 'bca_va',
            'VA Mandiri' => 'mandiri_va',
            'VA BNI' => 'bni_va',
            default => strtolower(str_replace(' ', '_', $payment)),
        };
    }
}
