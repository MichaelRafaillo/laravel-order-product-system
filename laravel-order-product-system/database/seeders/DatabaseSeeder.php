<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create test user
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Create sample products
        $products = [
            [
                'name' => 'Gold Necklace',
                'description' => 'Beautiful 18k gold necklace',
                'price' => 15000.00,
                'stock_quantity' => 10,
                'sku' => 'GOLD-NECK-001',
                'is_active' => true,
            ],
            [
                'name' => 'Silver Ring',
                'description' => 'Sterling silver ring with diamond',
                'price' => 5000.00,
                'stock_quantity' => 25,
                'sku' => 'SILVER-RING-001',
                'is_active' => true,
            ],
            [
                'name' => 'Diamond Earrings',
                'description' => 'Elegant diamond stud earrings',
                'price' => 12000.00,
                'stock_quantity' => 15,
                'sku' => 'DIAMOND-EAR-001',
                'is_active' => true,
            ],
            [
                'name' => 'Pearl Bracelet',
                'description' => 'Freshwater pearl bracelet',
                'price' => 3500.00,
                'stock_quantity' => 20,
                'sku' => 'PEARL-BRAC-001',
                'is_active' => true,
            ],
            [
                'name' => 'Ruby Pendant',
                'description' => 'Ruby pendant in white gold',
                'price' => 8500.00,
                'stock_quantity' => 8,
                'sku' => 'RUBY-PEND-001',
                'is_active' => true,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }

        // Create sample customers
        $customers = [
            [
                'name' => 'Ahmed Hassan',
                'email' => 'ahmed@example.com',
                'phone' => '+20 10 12345678',
                'address' => '123 Main St, Cairo',
                'is_active' => true,
            ],
            [
                'name' => 'Mohamed Ali',
                'email' => 'mohamed@example.com',
                'phone' => '+20 11 87654321',
                'address' => '456 Nile Ave, Alexandria',
                'is_active' => true,
            ],
            [
                'name' => 'Fatima Omar',
                'email' => 'fatima@example.com',
                'phone' => '+20 12 34567890',
                'address' => '789 Garden Rd, Giza',
                'is_active' => true,
            ],
        ];

        foreach ($customers as $customer) {
            Customer::create($customer);
        }

        // Create sample orders (using user IDs as customer_id references users table)
        $orders = [
            [
                'customer_id' => 1,
                'order_number' => 'ORD-2026-001',
                'status' => 'completed',
                'notes' => 'Gift wrap requested',
                'total_amount' => 20000.00,
            ],
            [
                'customer_id' => 1,
                'order_number' => 'ORD-2026-002',
                'status' => 'pending',
                'notes' => '',
                'total_amount' => 5000.00,
            ],
            [
                'customer_id' => 1,
                'order_number' => 'ORD-2026-003',
                'status' => 'processing',
                'notes' => 'Express shipping',
                'total_amount' => 12000.00,
            ],
        ];

        foreach ($orders as $order) {
            $createdOrder = Order::create($order);
            
            // Add order items
            $orderItems = [
                [
                    'product_id' => 1,
                    'quantity' => 1,
                    'unit_price' => 15000.00,
                    'subtotal' => 15000.00,
                ],
                [
                    'product_id' => 2,
                    'quantity' => 1,
                    'unit_price' => 5000.00,
                    'subtotal' => 5000.00,
                ],
            ];
            
            foreach ($orderItems as $item) {
                OrderItem::create(array_merge($item, ['order_id' => $createdOrder->id]));
            }
        }

        $this->command->info('Database seeded successfully!');
    }
}
