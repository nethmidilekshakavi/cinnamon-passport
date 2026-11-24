<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Order;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * SAMPLE DATA ONLY. These sellers, buyers, orders and ratings are invented
 * for the prototype demo. All demo accounts use the password "password".
 */
class DemoMarketSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(CategorySeeder::class);

        if (User::where('email', 'ceylongold@demo.test')->exists()) {
            $this->command?->info('Demo data already exists, nothing to do.');

            return;
        }

        $cats = Category::pluck('id', 'slug');
        $made = now()->subMonths(2)->toDateString();
        $exp = fn (int $months) => now()->addMonths($months)->toDateString();

        $brands = [
            [
                'email' => 'ceylongold@demo.test', 'owner' => 'Nimal Perera',
                'name' => 'Ceylon Gold Spice', 'district' => 'Galle', 'address' => '45 Temple Road, Habaraduwa',
                'description' => 'Family-owned cinnamon producers from Galle. We hand-roll Alba and C5 quills and ship worldwide.',
                'products' => [
                    ['Alba Cinnamon Sticks', 'cinnamon-sticks', 'high', 'Alba', '100% Ceylon cinnamon (Cinnamomum verum)', 100, 'g', 14.50, 20, true, 'organic', 'CGS-2611-A', 24],
                    ['C5 Cinnamon Sticks', 'cinnamon-sticks', 'medium', 'C5', '100% Ceylon cinnamon', 250, 'g', 11.00, 30, false, 'compost', 'CGS-2611-B', 24],
                    ['Ceylon Cinnamon Powder', 'cinnamon-powder', 'high', null, 'Ground Ceylon cinnamon, no additives', 150, 'g', 8.50, 40, true, 'organic', 'CGS-2611-C', 18],
                ],
            ],
            [
                'email' => 'kandyhills@demo.test', 'owner' => 'Saman Bandara',
                'name' => 'Kandy Hills Cinnamon', 'district' => 'Kandy', 'address' => '12 Hill Street, Peradeniya',
                'description' => 'Small growers in the Kandy hills. Our tea and oil are made from fresh bark and leaves.',
                'products' => [
                    ['Cinnamon Leaf Tea', 'cinnamon-tea', 'high', null, 'Dried Ceylon cinnamon bark and leaf', 50, 'g', 6.00, 50, true, 'organic', 'KHC-2610-T', 20],
                    ['Cinnamon Bark Tea with Ginger', 'cinnamon-tea', 'medium', null, 'Cinnamon bark, dried ginger, lemongrass', 60, 'g', 5.50, 35, false, 'compost', 'KHC-2610-G', 18],
                    ['Cinnamon Leaf Oil', 'cinnamon-oil', 'medium', null, 'Steam-distilled cinnamon leaf oil', 30, 'ml', 9.00, 25, false, 'inorganic', 'KHC-2609-O', 30],
                ],
            ],
            [
                'email' => 'matara@demo.test', 'owner' => 'Kamala Silva',
                'name' => 'Matara Spice Garden', 'district' => 'Matara', 'address' => '8 Coconut Lane, Weligama',
                'description' => 'Women-led cooperative producing cinnamon balm and body care from our own plantations.',
                'products' => [
                    ['Cinnamon Healing Balm', 'cinnamon-balm', 'high', null, 'Cinnamon bark oil, beeswax, coconut oil', 40, 'g', 7.50, 30, true, 'organic', 'MSG-2611-B', 24],
                    ['Cinnamon and Clove Balm', 'cinnamon-balm', 'medium', null, 'Cinnamon oil, clove oil, beeswax, shea butter', 40, 'g', 7.00, 22, false, 'compost', 'MSG-2611-C', 24],
                    ['Cinnamon Bark Oil', 'cinnamon-oil', 'high', null, 'Pure cinnamon bark essential oil', 10, 'ml', 28.00, 12, true, 'organic', 'MSG-2610-O', 36],
                ],
            ],
            [
                'email' => 'southernleaf@demo.test', 'owner' => 'Ruwan Jayasinghe',
                'name' => 'Southern Leaf Naturals', 'district' => 'Kalutara', 'address' => '3 Beach Road, Beruwala',
                'description' => 'Natural fragrance and low-cost everyday cinnamon for home cooks.',
                'products' => [
                    ['Cinnamon Spice Perfume', 'cinnamon-perfume', 'medium', null, 'Alcohol, cinnamon oil, vanilla extract', 50, 'ml', 18.00, 15, false, 'inorganic', 'SLN-2611-P', 24],
                    ['Cinnamon Chips (Cooking)', 'cinnamon-sticks', 'low', 'H1', 'Broken cinnamon quills and chips', 500, 'g', 6.50, 60, false, 'inorganic', 'SLN-2611-H', 18],
                    ['Cinnamon Powder (Economy)', 'cinnamon-powder', 'low', null, 'Ground cinnamon (chips and featherings)', 500, 'g', 5.00, 80, false, 'inorganic', 'SLN-2611-E', 12],
                ],
            ],
        ];

        $sellerBrands = [];

        foreach ($brands as $b) {
            $user = new User();
            $user->forceFill([
                'name' => $b['owner'], 'email' => $b['email'], 'password' => Hash::make('password'),
                'role' => 'buyer', 'email_verified_at' => now(),
            ])->save();

            $brand = Brand::create([
                'user_id' => $user->id, 'name' => $b['name'], 'district' => $b['district'],
                'address' => $b['address'], 'description' => $b['description'],
            ]);
            $sellerBrands[] = $brand;

            foreach ($b['products'] as [$name, $cat, $quality, $grade, $ingredients, $qty, $unit, $price, $stock, $organic, $method, $batch, $months]) {
                $brand->products()->create([
                    'category_id' => $cats[$cat], 'name' => $name, 'quality_level' => $quality, 'grade' => $grade,
                    'ingredients' => $ingredients, 'net_quantity' => $qty, 'unit' => $unit, 'price' => $price,
                    'stock' => $stock, 'is_organic' => $organic, 'growing_method' => $method, 'batch_code' => $batch,
                    'production_date' => $made, 'expiry_date' => $exp($months), 'is_published' => true,
                    'description' => 'SAMPLE product for the prototype demo.',
                ]);
            }
        }

        // Sample buyers with delivered orders and ratings
        $buyers = [
            ['Emma Wilson', 'emma@demo.test', 'London', 'United Kingdom'],
            ['Lukas Meyer', 'lukas@demo.test', 'Berlin', 'Germany'],
            ['Sophie Martin', 'sophie@demo.test', 'Lyon', 'France'],
        ];

        $ratings = [ // [buyerIndex, brandIndex, rating, comment]
            [0, 0, 5, 'Beautiful thin quills and a strong, sweet smell. Arrived well packed.'],
            [1, 0, 4, 'Good quality. Delivery took a little long.'],
            [2, 0, 5, 'Best Ceylon cinnamon I have bought.'],
            [0, 1, 5, 'Lovely tea, very fresh.'],
            [1, 1, 4, 'Nice flavour, will order again.'],
            [2, 2, 5, 'The balm is great for dry hands.'],
            [0, 2, 4, 'Good oil, small bottle.'],
            [1, 3, 3, 'Fine for cooking, not as fragrant as the premium ones.'],
            [2, 3, 4, 'Good price for everyday use.'],
        ];

        $buyerUsers = [];
        foreach ($buyers as [$name, $email]) {
            $u = new User();
            $u->forceFill([
                'name' => $name, 'email' => $email, 'password' => Hash::make('password'),
                'role' => 'buyer', 'email_verified_at' => now(),
            ])->save();
            $buyerUsers[] = $u;
        }

        foreach ($ratings as [$bi, $bri, $rating, $comment]) {
            $buyer = $buyerUsers[$bi];
            [, , $city, $country] = $buyers[$bi];
            $brand = $sellerBrands[$bri];
            $product = $brand->products()->first();

            $order = Order::create([
                'user_id' => $buyer->id, 'brand_id' => $brand->id, 'product_id' => $product->id,
                'product_name' => $product->name, 'product_size' => $product->net_quantity.' '.$product->unit,
                'unit_price' => $product->price, 'quantity' => 1, 'total' => $product->price,
                'shipping_name' => $buyer->name, 'shipping_address' => '1 Sample Street',
                'shipping_city' => $city, 'shipping_country' => $country,
                'status' => Order::DELIVERED, 'payment_method' => 'simulated',
                'placed_at' => now()->subDays(20), 'shipped_at' => now()->subDays(17), 'delivered_at' => now()->subDays(8),
            ]);
            $order->update(['order_number' => 'CT-'.str_pad((string) $order->id, 6, '0', STR_PAD_LEFT)]);

            Review::create([
                'order_id' => $order->id, 'user_id' => $buyer->id, 'brand_id' => $brand->id,
                'rating' => $rating, 'comment' => $comment,
            ]);
        }

        $this->command?->info('Demo data created: 4 brands, 12 products, 3 buyers, 9 rated orders.');
    }
}