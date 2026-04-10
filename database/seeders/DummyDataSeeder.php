<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DummyDataSeeder extends Seeder
{
    private int $orderCounter = 1;

    private array $customerProfiles = [
        ['name' => 'Rina Wulandari',     'email' => 'rina.w@example.com',    'phone' => '081355001001', 'address' => 'Jl. Sudirman No. 45, Kebayoran Baru',     'city' => 'Jakarta Selatan',   'province' => 'DKI Jakarta',         'postal' => '12190'],
        ['name' => 'Dewi Kartika',       'email' => 'dewi.k@example.com',    'phone' => '081355001002', 'address' => 'Jl. Dago No. 12',                         'city' => 'Bandung',           'province' => 'Jawa Barat',          'postal' => '40132'],
        ['name' => 'Siti Nurhaliza',     'email' => 'siti.n@example.com',    'phone' => '081355001003', 'address' => 'Jl. Pemuda No. 88',                        'city' => 'Surabaya',          'province' => 'Jawa Timur',          'postal' => '60271'],
        ['name' => 'Maya Putri',         'email' => 'maya.p@example.com',    'phone' => '081355001004', 'address' => 'Jl. Malioboro No. 22',                     'city' => 'Yogyakarta',        'province' => 'DI Yogyakarta',       'postal' => '55271'],
        ['name' => 'Fitri Handayani',    'email' => 'fitri.h@example.com',   'phone' => '081355001005', 'address' => 'Jl. Pandanaran No. 15',                    'city' => 'Semarang',          'province' => 'Jawa Tengah',         'postal' => '50134'],
        ['name' => 'Ayu Lestari',        'email' => 'ayu.l@example.com',     'phone' => '081355001006', 'address' => 'Jl. Gatot Subroto No. 30',                 'city' => 'Medan',             'province' => 'Sumatera Utara',      'postal' => '20112'],
        ['name' => 'Dian Permata',       'email' => 'dian.p@example.com',    'phone' => '081355001007', 'address' => 'Jl. Pettarani No. 18',                     'city' => 'Makassar',          'province' => 'Sulawesi Selatan',    'postal' => '90111'],
        ['name' => 'Nadia Safitri',      'email' => 'nadia.s@example.com',   'phone' => '081355001008', 'address' => 'Jl. Sunset Road No. 77',                   'city' => 'Denpasar',          'province' => 'Bali',                'postal' => '80232'],
        ['name' => 'Laila Azzahra',      'email' => 'laila.a@example.com',   'phone' => '081355001009', 'address' => 'Jl. Ijen No. 56',                          'city' => 'Malang',            'province' => 'Jawa Timur',          'postal' => '65141'],
        ['name' => 'Anisa Rahma',        'email' => 'anisa.r@example.com',   'phone' => '081355001010', 'address' => 'Jl. Slamet Riyadi No. 99',                 'city' => 'Solo',              'province' => 'Jawa Tengah',         'postal' => '57126'],
        ['name' => 'Bunga Melati',       'email' => 'bunga.m@example.com',   'phone' => '081355001011', 'address' => 'Jl. Margonda Raya No. 33',                 'city' => 'Depok',             'province' => 'Jawa Barat',          'postal' => '16411'],
        ['name' => 'Citra Dewi',         'email' => 'citra.d@example.com',   'phone' => '081355001012', 'address' => 'Jl. Ahmad Yani No. 42',                    'city' => 'Bekasi',            'province' => 'Jawa Barat',          'postal' => '17144'],
        ['name' => 'Farah Adelia',       'email' => 'farah.a@example.com',   'phone' => '081355001013', 'address' => 'Jl. Pajajaran No. 61',                     'city' => 'Bogor',             'province' => 'Jawa Barat',          'postal' => '16151'],
        ['name' => 'Gita Savitri',       'email' => 'gita.s@example.com',    'phone' => '081355001014', 'address' => 'Jl. Jendral Sudirman No. 28',              'city' => 'Palembang',         'province' => 'Sumatera Selatan',    'postal' => '30132'],
        ['name' => 'Hana Pertiwi',       'email' => 'hana.p@example.com',    'phone' => '081355001015', 'address' => 'Jl. Jendral Sudirman No. 14',              'city' => 'Balikpapan',        'province' => 'Kalimantan Timur',    'postal' => '76111'],
        ['name' => 'Indah Permatasari',  'email' => 'indah.p@example.com',   'phone' => '081355001016', 'address' => 'Jl. Sam Ratulangi No. 55',                 'city' => 'Manado',            'province' => 'Sulawesi Utara',      'postal' => '95113'],
        ['name' => 'Jasmine Wijaya',     'email' => 'jasmine.w@example.com', 'phone' => '081355001017', 'address' => 'Jl. Puri Indah No. 9',                     'city' => 'Jakarta Barat',     'province' => 'DKI Jakarta',         'postal' => '11440'],
        ['name' => 'Kartini Susanti',    'email' => 'kartini.s@example.com', 'phone' => '081355001018', 'address' => 'Jl. Raya Bekasi No. 27',                   'city' => 'Jakarta Timur',     'province' => 'DKI Jakarta',         'postal' => '13510'],
        ['name' => 'Linda Marlina',      'email' => 'linda.m@example.com',   'phone' => '081355001019', 'address' => 'Jl. Baros No. 10',                         'city' => 'Cimahi',            'province' => 'Jawa Barat',          'postal' => '40511'],
        ['name' => 'Mega Puspita',       'email' => 'mega.p@example.com',    'phone' => '081355001020', 'address' => 'Jl. Basuki Rahmat No. 66',                 'city' => 'Surabaya',          'province' => 'Jawa Timur',          'postal' => '60275'],
        ['name' => 'Nina Agustina',      'email' => 'nina.a@example.com',    'phone' => '081355001021', 'address' => 'Jl. Pluit Raya No. 71',                    'city' => 'Jakarta Utara',     'province' => 'DKI Jakarta',         'postal' => '14240'],
        ['name' => 'Olivia Tandean',     'email' => 'olivia.t@example.com',  'phone' => '081355001022', 'address' => 'Jl. Alam Sutera No. 3',                    'city' => 'Tangerang Selatan', 'province' => 'Banten',              'postal' => '15414'],
        ['name' => 'Putri Maharani',     'email' => 'putri.m@example.com',   'phone' => '081355001023', 'address' => 'Jl. Pasteur No. 35',                       'city' => 'Bandung',           'province' => 'Jawa Barat',          'postal' => '40264'],
        ['name' => 'Qori Aisyah',        'email' => 'qori.a@example.com',    'phone' => '081355001024', 'address' => 'Jl. Kaliurang KM 5 No. 20',               'city' => 'Sleman',            'province' => 'DI Yogyakarta',       'postal' => '55281'],
        ['name' => 'Ratna Sari',         'email' => 'ratna.s@example.com',   'phone' => '081355001025', 'address' => 'Jl. Diponegoro No. 48',                    'city' => 'Semarang',          'province' => 'Jawa Tengah',         'postal' => '50241'],
        ['name' => 'Sari Mulyani',       'email' => 'sari.m@example.com',    'phone' => '081355001026', 'address' => 'Jl. Fatmawati No. 17',                     'city' => 'Jakarta Selatan',   'province' => 'DKI Jakarta',         'postal' => '12520'],
        ['name' => 'Tania Anggraeni',    'email' => 'tania.a@example.com',   'phone' => '081355001027', 'address' => 'Jl. Braga No. 80',                         'city' => 'Bandung',           'province' => 'Jawa Barat',          'postal' => '40154'],
    ];

    /**
     * Weighted product popularity (product_id => weight).
     * Higher weight = appears more often in orders.
     */
    private array $productWeights = [
        1  => 15,  // Niacinamide - bestseller
        2  => 8,   // Hydrating Cleanser
        3  => 6,   // Effaclar Duo
        4  => 8,   // Hydro Boost Water Gel
        5  => 10,  // Daily Facial Cleanser
        6  => 10,  // Green Tea Fresh Toner
        7  => 9,   // Snail Mucin Essence
        8  => 8,   // AHA BHA PHA Toner
        9  => 14,  // Ceramide Barrier - bestseller
        10 => 5,   // Acne Gentle Cleansing
        11 => 4,   // Lightening Face Toner
        12 => 11,  // Bright Stuff Face Wash
        13 => 7,   // Hyaluronic Acid
        14 => 6,   // Your Skin Bae
        15 => 16,  // Sunscreen - top seller
    ];

    private array $reviewComments = [
        5 => [
            'Produk luar biasa! Kulit jadi lebih cerah dan halus dalam 2 minggu pemakaian.',
            'Sangat recommended! Sudah repurchase 3x dan hasilnya konsisten bagus.',
            'Best skincare ever, hasilnya nyata dan cepat terlihat!',
            'Worth every penny, kulit jadi glowing dan sehat.',
            'Favorit banget, ga bisa pindah ke lain hati. Wajib punya!',
            'Teksturnya ringan dan cepat menyerap, kulit terasa segar sepanjang hari.',
            'Awalnya ragu tapi setelah coba langsung jatuh cinta. Top!',
        ],
        4 => [
            'Bagus, kulit terasa lebih lembab dan kenyal.',
            'Cocok di kulit aku, hasilnya lumayan cepat terlihat.',
            'Suka teksturnya, ringan dan tidak lengket di kulit.',
            'Cukup efektif, perlu waktu 3-4 minggu untuk lihat hasil maksimal.',
            'Recommended, harga sebanding dengan kualitas yang didapat.',
            'Lumayan bagus, kulit terasa lebih halus setelah pemakaian rutin.',
        ],
        3 => [
            'Lumayan, tapi tidak sesuai ekspektasi awal.',
            'Biasa aja, tidak terlalu terasa efeknya di kulit aku.',
            'Oke lah untuk harganya, cocok untuk daily use.',
            'Tidak buruk tapi juga tidak wow, mungkin kurang cocok di kulit aku.',
            'Hasilnya standar, tapi packagingnya bagus dan travel-friendly.',
        ],
        2 => [
            'Kurang cocok di kulit aku, bikin sedikit kemerahan.',
            'Teksturnya terlalu lengket dan berat di kulit.',
            'Hasilnya belum terlihat setelah 3 minggu pemakaian rutin.',
            'Tidak sesuai deskripsi, terlalu wangi untuk kulit sensitif.',
        ],
        1 => [
            'Tidak cocok sama sekali, bikin kulit breakout parah.',
            'Sangat mengecewakan, tidak ada perubahan sama sekali.',
        ],
    ];

    public function run(): void
    {
        $this->command->info('🧹 Clearing existing order & review data...');
        $this->clearExistingData();

        $this->command->info('👥 Creating additional customers...');
        $this->createCustomers();

        $this->command->info('📦 Generating orders with upward sales trend...');
        $this->generateOrders();

        $this->command->info('⭐ Generating product reviews...');
        $this->generateReviews();

        $this->command->info('');
        $this->command->info('✅ Dummy data seeding complete!');
        $this->printSummary();
    }

    private function clearExistingData(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        OrderItem::truncate();
        Order::truncate();
        Review::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    private function createCustomers(): void
    {
        $password = Hash::make('password');

        foreach ($this->customerProfiles as $profile) {
            if (User::where('email', $profile['email'])->exists()) {
                continue;
            }

            $user = new User();
            $user->name = $profile['name'];
            $user->email = $profile['email'];
            $user->password = $password;
            $user->phone = $profile['phone'];
            $user->address = $profile['address'] . ', ' . $profile['city'];
            $user->email_verified_at = now();
            $user->save();
        }

        $this->command->info("  → " . count($this->customerProfiles) . " customer profiles processed");
    }

    private function generateOrders(): void
    {
        $products = Product::all();
        $customers = User::where('email', '!=', 'admin@ebeauty.com')->get();

        if ($products->isEmpty() || $customers->isEmpty()) {
            $this->command->warn('No products or customers found. Run base seeders first (php artisan db:seed).');
            return;
        }

        /**
         * Monthly order plan - upward trend with seasonal patterns.
         * Oct: soft launch, Nov: growing, Dec: holiday peak,
         * Jan: post-holiday dip, Feb: Valentine's recovery,
         * Mar: strong growth, Apr (partial): high daily rate.
         */
        $monthlyPlan = [
            ['year' => 2025, 'month' => 10, 'count' => 15, 'max_day' => 31],
            ['year' => 2025, 'month' => 11, 'count' => 22, 'max_day' => 30],
            ['year' => 2025, 'month' => 12, 'count' => 38, 'max_day' => 31],
            ['year' => 2026, 'month' => 1,  'count' => 25, 'max_day' => 31],
            ['year' => 2026, 'month' => 2,  'count' => 40, 'max_day' => 28],
            ['year' => 2026, 'month' => 3,  'count' => 55, 'max_day' => 31],
            ['year' => 2026, 'month' => 4,  'count' => 22, 'max_day' => 10],
        ];

        $paymentMethods = ['bank_transfer', 'ewallet', 'credit_card', 'cod'];
        $paymentWeights = [35, 30, 20, 15];

        $couriers        = ['JNE', 'J&T Express', 'SiCepat', 'AnterAja', 'Pos Indonesia'];
        $courierWeights  = [30, 25, 20, 15, 10];

        $services     = ['REG', 'YES', 'OKE'];
        $shippingCosts = [9000, 10000, 12000, 15000, 18000, 20000, 22000, 25000];

        $totalOrders = 0;
        $totalItems = 0;

        foreach ($monthlyPlan as $month) {
            $dates = [];
            for ($i = 0; $i < $month['count']; $i++) {
                $day = mt_rand(1, $month['max_day']);
                $dates[] = Carbon::create($month['year'], $month['month'], $day, mt_rand(7, 22), mt_rand(0, 59), mt_rand(0, 59));
            }
            sort($dates);

            foreach ($dates as $idx => $date) {
                $customer = $customers->random();
                $profile = $this->findCustomerProfile($customer);

                $numItems = $this->weightedRandom([1 => 20, 2 => 50, 3 => 25, 4 => 5]);
                $selectedProducts = $this->selectWeightedProducts($products, $numItems);

                $orderItems = [];
                $subtotal = 0;

                foreach ($selectedProducts as $product) {
                    $qty = $this->weightedRandom([1 => 65, 2 => 28, 3 => 7]);
                    $price = $product->discount_price ?? $product->price;
                    $lineTotal = $price * $qty;
                    $subtotal += $lineTotal;

                    $orderItems[] = [
                        'product_id'   => $product->id,
                        'product_name' => $product->name,
                        'product_sku'  => $product->sku,
                        'unit_price'   => $price,
                        'quantity'     => $qty,
                        'line_total'   => $lineTotal,
                    ];
                }

                $shippingCost = $shippingCosts[array_rand($shippingCosts)];
                $total = $subtotal + $shippingCost;

                $statusConfig = $this->assignStatus($idx, $month['count']);
                $paymentMethod = $this->weightedSelect($paymentMethods, $paymentWeights);
                $courier = $this->weightedSelect($couriers, $courierWeights);

                $orderNum = 'EB' . $date->format('Ymd') . str_pad($this->orderCounter, 4, '0', STR_PAD_LEFT);

                $order = Order::create([
                    'user_id'              => $customer->id,
                    'order_number'         => $orderNum,
                    'status'               => $statusConfig['status'],
                    'payment_status'       => $statusConfig['payment_status'],
                    'payment_method'       => $paymentMethod,
                    'subtotal'             => $subtotal,
                    'shipping_cost'        => $shippingCost,
                    'total'                => $total,
                    'shipping_name'        => $customer->name,
                    'shipping_phone'       => $profile['phone'],
                    'shipping_address_line'=> $profile['address'],
                    'shipping_city'        => $profile['city'],
                    'shipping_province'    => $profile['province'],
                    'shipping_postal_code' => $profile['postal'],
                    'shipping_courier'     => $courier,
                    'shipping_service'     => $services[array_rand($services)],
                    'created_at'           => $date,
                    'updated_at'           => $date,
                ]);

                foreach ($orderItems as $item) {
                    OrderItem::create(array_merge($item, [
                        'order_id'   => $order->id,
                        'created_at' => $date,
                        'updated_at' => $date,
                    ]));
                    $totalItems++;
                }

                $this->orderCounter++;
                $totalOrders++;
            }

            $label = Carbon::create($month['year'], $month['month'])->translatedFormat('M Y');
            $this->command->info("  → {$label}: {$month['count']} orders");
        }

        $this->command->info("  → Total: {$totalOrders} orders, {$totalItems} items");
    }

    /**
     * Assign order status based on position in the month.
     * Earlier orders are more likely completed; later ones might still be in progress.
     */
    private function assignStatus(int $index, int $total): array
    {
        $pct = ($index / max($total, 1)) * 100;

        if ($pct > 96) {
            return ['status' => 'pending_payment', 'payment_status' => 'pending'];
        }
        if ($pct > 93) {
            return ['status' => 'cancelled', 'payment_status' => 'pending'];
        }
        if ($pct > 88) {
            return ['status' => 'processing', 'payment_status' => 'paid'];
        }
        if ($pct > 80) {
            return ['status' => 'shipped', 'payment_status' => 'paid'];
        }
        if ($pct > 45) {
            return ['status' => 'completed', 'payment_status' => 'paid'];
        }

        return ['status' => 'completed', 'payment_status' => 'paid'];
    }

    /**
     * Select N products using weighted probability so popular products appear more often.
     */
    private function selectWeightedProducts($products, int $count): array
    {
        $selected = [];
        $usedIds = [];
        $maxAttempts = $count * 10;
        $attempts = 0;

        while (count($selected) < $count && $attempts < $maxAttempts) {
            $attempts++;
            $product = $this->pickWeightedProduct($products);

            if (!in_array($product->id, $usedIds)) {
                $selected[] = $product;
                $usedIds[] = $product->id;
            }
        }

        return $selected;
    }

    private function pickWeightedProduct($products)
    {
        $totalWeight = 0;
        $weighted = [];

        foreach ($products as $product) {
            $w = $this->productWeights[$product->id] ?? 5;
            $totalWeight += $w;
            $weighted[] = ['product' => $product, 'cumulative' => $totalWeight];
        }

        $roll = mt_rand(1, $totalWeight);

        foreach ($weighted as $entry) {
            if ($roll <= $entry['cumulative']) {
                return $entry['product'];
            }
        }

        return $products->first();
    }

    private function findCustomerProfile(User $customer): array
    {
        foreach ($this->customerProfiles as $profile) {
            if ($profile['email'] === $customer->email) {
                return $profile;
            }
        }

        return [
            'phone'    => $customer->phone ?? '0812' . mt_rand(10000000, 99999999),
            'address'  => $customer->address ?? 'Jl. Thamrin No. 1',
            'city'     => 'Jakarta Pusat',
            'province' => 'DKI Jakarta',
            'postal'   => '10110',
        ];
    }

    private function generateReviews(): void
    {
        $products = Product::all();
        $customers = User::where('email', '!=', 'admin@ebeauty.com')->get();

        if ($customers->count() < 3) {
            return;
        }

        $reviewCount = 0;

        foreach ($products as $product) {
            $numReviews = mt_rand(3, min(8, $customers->count()));
            $reviewers = $customers->random($numReviews);

            foreach ($reviewers as $reviewer) {
                if (Review::where('product_id', $product->id)->where('user_id', $reviewer->id)->exists()) {
                    continue;
                }

                $rating = $this->weightedRandom([5 => 35, 4 => 30, 3 => 20, 2 => 10, 1 => 5]);
                $comments = $this->reviewComments[$rating];
                $comment = $comments[array_rand($comments)];

                Review::create([
                    'product_id'          => $product->id,
                    'user_id'             => $reviewer->id,
                    'rating'              => $rating,
                    'comment'             => $comment,
                    'is_verified_purchase' => mt_rand(0, 100) < 70,
                    'is_approved'         => true,
                    'created_at'          => Carbon::now()->subDays(mt_rand(1, 180)),
                    'updated_at'          => Carbon::now()->subDays(mt_rand(0, 30)),
                ]);

                $reviewCount++;
            }
        }

        $this->command->info("  → {$reviewCount} reviews created");
    }

    private function weightedRandom(array $weightedValues): int
    {
        $total = array_sum($weightedValues);
        $roll = mt_rand(1, $total);

        foreach ($weightedValues as $value => $weight) {
            $roll -= $weight;
            if ($roll <= 0) {
                return $value;
            }
        }

        return array_key_first($weightedValues);
    }

    private function weightedSelect(array $items, array $weights): string
    {
        $total = array_sum($weights);
        $roll = mt_rand(1, $total);

        foreach ($items as $i => $item) {
            $roll -= $weights[$i];
            if ($roll <= 0) {
                return $item;
            }
        }

        return $items[0];
    }

    private function printSummary(): void
    {
        $totalOrders = Order::count();
        $paidOrders = Order::where('payment_status', 'paid')->count();
        $totalRevenue = Order::where('payment_status', 'paid')
            ->whereNotIn('status', ['cancelled'])
            ->sum('subtotal');
        $totalItems = OrderItem::count();
        $totalReviews = Review::count();
        $totalCustomers = User::where('email', '!=', 'admin@ebeauty.com')->count();

        $this->command->info('');
        $this->command->info('╔══════════════════════════════════════════╗');
        $this->command->info('║         📊 DATA SUMMARY                 ║');
        $this->command->info('╠══════════════════════════════════════════╣');
        $this->command->info("║  Customers    : {$totalCustomers}");
        $this->command->info("║  Total Orders : {$totalOrders}");
        $this->command->info("║  Paid Orders  : {$paidOrders}");
        $this->command->info("║  Order Items  : {$totalItems}");
        $this->command->info("║  Reviews      : {$totalReviews}");
        $this->command->info("║  Revenue      : Rp " . number_format($totalRevenue, 0, ',', '.'));
        $this->command->info('╚══════════════════════════════════════════╝');
        $this->command->info('');
        $this->command->info('💡 Export to SQL: mysqldump -u root db_beauty --no-create-info > dummy_data.sql');
    }
}
