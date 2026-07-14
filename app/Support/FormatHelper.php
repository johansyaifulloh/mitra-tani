<?php

namespace App\Support;

class FormatHelper
{
    public static function rupiah(float|int|string $amount): string
    {
        return 'Rp '.number_format((float) $amount, 0, ',', '.');
    }

    public static function soldLabel(int $count): string
    {
        if ($count <= 0) {
            return '0 terjual';
        }

        if ($count >= 1000) {
            $value = $count >= 10000
                ? (int) round($count / 1000)
                : round($count / 1000, 1);

            return str_replace('.', ',', (string) $value).' rb+ terjual';
        }

        return number_format($count, 0, ',', '.').' terjual';
    }

    public static function productForMobile(object $row, ?string $categoryName = null): array
    {
        $sold = (int) ($row->sold_count ?? 0);

        return [
            'id' => $row->id,
            'name' => $row->name,
            'cat' => $categoryName ?? ($row->category_name ?? ''),
            'price' => self::rupiah($row->price),
            'unit_price' => (float) $row->price,
            'badge' => $row->badge,
            'emoji' => $row->emoji ?? '🌱',
            'slug' => $row->slug,
            'stock' => (int) $row->stock,
            'sold' => $sold,
            'sold_label' => self::soldLabel($sold),
            'description' => $row->description ?? '',
            'image_path' => $row->image_path ?? null,
        ];
    }

    public static function categoryForView(object $row): array
    {
        return [
            'id' => $row->id,
            'name' => $row->name,
            'icon' => $row->icon,
            'slug' => $row->slug,
            'color' => $row->color,
            'count' => (int) ($row->product_count ?? 0),
        ];
    }

    public static function addressForView(object $row): array
    {
        return [
            'id' => $row->id,
            'label' => $row->label,
            'name' => $row->recipient_name,
            'phone' => $row->phone,
            'street' => $row->street,
            'district' => $row->district,
            'default' => (bool) $row->is_default,
        ];
    }

    public static function cartItemForView(object $row): array
    {
        return [
            'id' => $row->id,
            'product_id' => $row->product_id,
            'emoji' => $row->emoji ?? '🌱',
            'name' => $row->product_name,
            'unit' => (int) $row->price,
            'price' => self::rupiah($row->price),
            'qty' => (int) $row->quantity,
            'is_selected' => (bool) $row->is_selected,
        ];
    }

    public static function paymentMethodLabel(?string $method): string
    {
        if (! $method) {
            return '—';
        }

        $labels = [
            'qris' => 'QRIS',
            'bank_transfer' => 'Virtual Account',
            'bca_va' => 'BCA Virtual Account',
            'bni_va' => 'BNI Virtual Account',
            'bri_va' => 'BRI Virtual Account',
            'mandiri_va' => 'Mandiri Virtual Account',
            'permata_va' => 'Permata Virtual Account',
            'cimb_va' => 'CIMB Virtual Account',
            'echannel' => 'Mandiri Virtual Account',
            'gopay' => 'GoPay',
            'shopeepay' => 'ShopeePay',
            'dana' => 'DANA',
            'cstore' => 'Gerai Retail',
            'indomaret' => 'Indomaret',
            'alfamart' => 'Alfamart',
            'credit_card' => 'Kartu Kredit',
        ];

        return $labels[$method] ?? ucwords(str_replace('_', ' ', $method));
    }

    public static function paymentStatusLabel(?string $status): array
    {
        return match ($status) {
            'lunas' => ['label' => 'Lunas', 'tone' => 'success'],
            'menunggu_pembayaran' => ['label' => 'Belum Bayar', 'tone' => 'warning'],
            'expired' => ['label' => 'Kadaluarsa', 'tone' => 'danger'],
            default => ['label' => ucwords(str_replace('_', ' ', (string) $status)), 'tone' => 'neutral'],
        };
    }

    public static function pickupStatusLabel(?string $status): array
    {
        return match ($status) {
            'menunggu_approval' => ['label' => 'Menunggu Pengambilan', 'tone' => 'warning', 'desc' => 'Pembayaran lunas. Tunjukkan kode transaksi saat mengambil barang di toko.'],
            'disetujui' => ['label' => 'Sudah Diambil', 'tone' => 'success', 'desc' => 'Barang sudah diambil & diverifikasi admin. Terima kasih!'],
            'selesai' => ['label' => 'Selesai', 'tone' => 'success', 'desc' => 'Transaksi selesai. Terima kasih!'],
            'ditolak' => ['label' => 'Ditolak', 'tone' => 'danger', 'desc' => 'Pengambilan ditolak/dibatalkan. Hubungi admin toko.'],
            default => ['label' => '', 'tone' => 'neutral', 'desc' => ''],
        };
    }

    public static function orderForAdmin(object $row): array
    {
        $status = $row->pickup_status ?? $row->payment_status;

        return [
            'id' => $row->code,
            'order_id' => $row->id,
            'customer' => $row->buyer_name,
            'total' => (float) $row->total,
            'date' => date('d/m/Y', strtotime($row->created_at)),
            'payment' => self::paymentMethodLabel($row->payment_method ?? null),
            'payment_status' => $row->payment_status,
            'status' => $status,
            'phone' => $row->buyer_phone,
            'address' => $row->address_text ?? '—',
            'pickup_proof' => null,
        ];
    }
}
