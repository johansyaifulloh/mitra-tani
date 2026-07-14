@extends('layouts.owner.app', ['navActive' => 'owner.dashboard'])
@section('page-title', 'Dashboard Owner')
@section('page-sub', 'Insight penjualan toko')
@section('content')
<div class="grid sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-8">
<div class="bg-white rounded-2xl p-6 shadow-soft border"><p class="text-xs text-gray-500 uppercase font-semibold">Pendapatan Bulan Ini</p><p class="text-3xl font-extrabold text-brand-700 mt-2">Rp 12,4 jt</p></div>
<div class="bg-white rounded-2xl p-6 shadow-soft border"><p class="text-xs text-gray-500 uppercase font-semibold">Transaksi</p><p class="text-3xl font-extrabold mt-2">156</p></div>
<div class="bg-white rounded-2xl p-6 shadow-soft border"><p class="text-xs text-gray-500 uppercase font-semibold">Terlaris</p><p class="text-lg font-bold mt-2">Pupuk Urea</p></div>
<div class="bg-white rounded-2xl p-6 shadow-soft border"><p class="text-xs text-gray-500 uppercase font-semibold">Konversi</p><p class="text-3xl font-extrabold mt-2">91%</p></div>
</div>
<div class="bg-white rounded-2xl border shadow-soft p-8 h-72 flex items-center justify-center text-gray-400">📈 Grafik Pendapatan 6 Bulan</div>
@endsection
