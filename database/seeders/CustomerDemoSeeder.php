<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class CustomerDemoSeeder extends Seeder
{
    public function run(): void
    {
        $andi = User::query()->where('email', 'andi@example.com')->firstOrFail();

        Address::query()->updateOrCreate(
            ['user_id' => $andi->id, 'label' => 'Rumah'],
            [
                'recipient_name' => 'Andi Wijaya',
                'phone' => '0812-3456-7890',
                'street' => 'Jl. Melati No. 12, RT 03/RW 05',
                'district' => 'Selorejo, Malang 65100',
                'is_default' => true,
            ],
        );

        Address::query()->updateOrCreate(
            ['user_id' => $andi->id, 'label' => 'Kebun'],
            [
                'recipient_name' => 'Andi Wijaya',
                'phone' => '0812-3456-7890',
                'street' => 'Dusun Krajan, Blok C2',
                'district' => 'Selorejo, Malang 65100',
                'is_default' => false,
            ],
        );

        $urea = Product::query()->where('slug', 'pupuk-urea-50kg')->firstOrFail();
        $cabai = Product::query()->where('slug', 'benih-cabai-f1')->firstOrFail();

        CartItem::query()->updateOrCreate(
            ['user_id' => $andi->id, 'product_id' => $urea->id],
            ['quantity' => 2, 'is_selected' => true],
        );

        CartItem::query()->updateOrCreate(
            ['user_id' => $andi->id, 'product_id' => $cabai->id],
            ['quantity' => 1, 'is_selected' => true],
        );
    }
}
