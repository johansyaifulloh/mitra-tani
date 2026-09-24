<?php

namespace Tests\Feature;

use App\Repositories\AddressRepository;
use App\Repositories\CartRepository;
use App\Repositories\ProductRepository;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class StockCheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_decrements_product_stock_and_restores_on_expire(): void
    {
        // 1. Setup user, category, product, address, cart
        $userId = DB::table('users')->insertGetId([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'role' => 'customer',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $catId = DB::table('categories')->insertGetId([
            'name' => 'Pupuk',
            'slug' => 'pupuk',
            'icon' => '🌱',
            'color' => '#10B981',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $productId = DB::table('products')->insertGetId([
            'category_id' => $catId,
            'name' => 'Pupuk Urea 50kg',
            'slug' => 'pupuk-urea-50kg',
            'price' => 100000,
            'stock' => 50,
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $addressId = DB::table('addresses')->insertGetId([
            'user_id' => $userId,
            'label' => 'Rumah',
            'recipient_name' => 'Test User',
            'phone' => '08123456789',
            'street' => 'Jl. Merdeka',
            'district' => 'Kec. Sukajadi',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('cart_items')->insert([
            'user_id' => $userId,
            'product_id' => $productId,
            'quantity' => 5,
            'is_selected' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Perform checkout via OrderService
        /** @var OrderService $orderService */
        $orderService = app(OrderService::class);
        
        // Mock Midtrans snap token creation if needed or let it proceed if mockable
        // Note: MidtransService::createSnapToken might fail in test without key unless mocked
        $this->mock(\App\Services\MidtransService::class, function ($mock) {
            $mock->shouldReceive('createSnapToken')->andReturn('dummy-snap-token');
            $mock->shouldReceive('getTransactionStatus')->andReturn(null);
        });

        $orderService = app(OrderService::class);
        $order = $orderService->createFromCheckout($userId, ['address_id' => $addressId]);

        // 3. Assert product stock decreased from 50 to 45
        $product = DB::table('products')->where('id', $productId)->first();
        $this->assertEquals(45, $product->stock);

        // 4. Test status change to expire restores stock back to 50
        $orderService->syncFromSnapResult($order->code, $userId, [
            'transaction_status' => 'expire',
        ]);

        $productAfterExpire = DB::table('products')->where('id', $productId)->first();
        $this->assertEquals(50, $productAfterExpire->stock);
    }
}
