@extends('layouts.owner.app', ['navActive' => 'owner.laporan.index'])
@section('page-title', 'Laporan Penjualan')
@section('page-sub', 'Export & analisis transaksi')
@section('content')
<div class="flex flex-wrap gap-3 mb-6"><input type="date" class="border rounded-xl px-4 py-2 text-sm"><input type="date" class="border rounded-xl px-4 py-2 text-sm"><button class="bg-brand-600 text-white px-5 py-2 rounded-xl font-semibold text-sm">Filter</button><button class="border px-5 py-2 rounded-xl text-sm">Export PDF</button></div>
<div class="bg-white rounded-2xl shadow-soft border overflow-x-auto"><table class="w-full text-sm"><thead class="bg-gray-50 text-xs uppercase text-gray-500 text-left"><tr><th class="px-6 py-4">ID</th><th class="px-6 py-4">Tanggal</th><th class="px-6 py-4">Pelanggan</th><th class="px-6 py-4">Total</th><th class="px-6 py-4">Metode Bayar</th><th class="px-6 py-4">Status</th></tr></thead>
<tbody class="divide-y"><tr><td class="px-6 py-4 font-mono text-xs">#TRX-001</td><td class="px-6 py-4">05/07/2026</td><td class="px-6 py-4">Andi Wijaya</td><td class="px-6 py-4 font-medium">Rp 197.500</td><td class="px-6 py-4">QRIS</td><td class="px-6 py-4"><span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full">Lunas</span></td></tr></tbody></table></div>
@endsection
