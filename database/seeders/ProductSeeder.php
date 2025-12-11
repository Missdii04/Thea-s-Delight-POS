<?php

namespace Database\Seeders;

use App\Models\Product; // <--- Make sure this model is used
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Clear old products before inserting new ones
        Product::truncate(); 
        
        $products = [
            // --- CAKES (Category: Cake) ---
            [
                'name' => 'Chocolate Dream Cake',
                'category' => 'Chocolate Cake',
                'price' => 650.00,
                'stock_quantity' => 10,
                'description' => 'Rich, moist chocolate layers filled with smooth chocolate ganache.',
                'sku' => 'choc101',
            ],
             [
                'name' => 'Mango Float Cake',
                'category' => 'Refrigerated Cake',
                'price' => 680.00,
                'stock_quantity' => 3,
                'description' => 'Light graham cake layered with fresh mangoes and sweet cream.',
                'sku' => 'ref101',
            ],
            [
                'name' => 'Classic Red Velvet',
                'category' => 'Specialty Cake',
                'price' => 660.00,
                'stock_quantity' => 12,
                'description' => 'Soft red velvet sponge with creamy cream cheese frosting.',
                'sku' => 'spec101',
            ],
            // --- COOKIES (Category: Pastry) ---
            [
                'name' => 'Ube Supreme Cake',
                'category' => 'Filipino Cake',
                'price' => 590.00,
                'stock_quantity' => 4,
                'description' => 'Moist ube sponge with halaya filling and whipped cream frosting.',
                'sku' => 'fil101',
            ],
            [
                'name' => 'Caramel Crunch Cake',
                'category' => 'Caramel Cake',
                'price' => 555.00,
                'stock_quantity' => 10,
                'description' => 'Soft vanilla cake topped with caramel glaze and crunchy toffee bits.', 
                'sku' => 'caram101',
            ],
            [
                'name' => 'Strawberry Shortcake',
                'category' => 'Fruit & Vegetable Cake',
                'price' => 570,
                'stock_quantity' => 9,
                'description' => 'Fluffy sponge cake with fresh strawberries and whipped cream.', 
                'sku' => 'fvg103',
            ],
            [
                'name' => 'Black Forest Cake',
                'category' => 'Chocolate Cake',
                'price' => 650,
                'stock_quantity' => 15,
                'description' => 'Chocolate layers with cherries, whipped cream, and chocolate shavings.', 
                'sku' => 'choc102',
            ],
            [
                'name' => 'Mocha Espresso Cake',
                'category' => 'Coffee Cake',
                'price' => 600,
                'stock_quantity' => 13,
                'description' => 'Light mocha sponge with espresso buttercream.', 
                'sku' => 'coff101',
            ],
            [
                'name' => 'Blueberry Cheesecake',
                'category' => 'Cheese Cake',
                'price' => 450,
                'stock_quantity' => 11,
                'description' => 'Classic creamy cheesecake topped with blueberry compote.', 
                'sku' => 'cheese101',
            ],
            [
                'name' => 'Oreo Cookies & Cream',
                'category' => 'Chocolate Cake',
                'price' => 630,
                'stock_quantity' =>12,
                'description' => 'Chocolate sponge with Oreo cream filling and crushed cookies.', 
                'sku' => 'choc103',
            ],
            [
                'name' => 'Tres Leches Cake',
                'category' => 'Milk Cake',
                'price' =>430,
                'stock_quantity' =>8 ,
                'description' => 'Soft sponge soaked in three kinds of milk for a sweet creamy finish.', 
                'sku' => 'mil101',
            ],
            [
                'name' => 'Carrot Walnut Cake',
                'category' => 'Fruit & Vegetable Cake',
                'price' =>490 ,
                'stock_quantity' => 5,
                'description' => 'Moist carrot cake with walnuts and smooth cream cheese frosting.', 
                'sku' => 'fvg102',
                ],
            [
                'name' => 'Lemon Zest Cake',
                'category' => 'Fruit & Vegetable Cake',
                'price' => 675 ,
                'stock_quantity' =>7 ,
                'description' => 'Light lemon sponge with tangy lemon buttercream.', 
                'sku' => 'fvg101',
                
            ],
            [
                'name' => 'Biscoff Burnt Basque',
                'category' => 'Cheese Cake',
                'price' => 760,
                'stock_quantity' => 11,
                'description' => 'Creamy burnt cheesecake topped with Biscoff spread.', 
                'sku' => 'cheese102',
            ],
            [
                'name' => 'Chocolate Truffle Cake',
                'category' => 'Chocolate Cake',
                'price' =>440 ,
                'stock_quantity' =>14 ,
                'description' => 'Deep chocolate cake filled with silky truffle ganache.', 
                'sku' => 'choc104',
            ],
            [
                'name' => 'Pandan Coconut Cake',
                'category' => 'Filipino Cake',
                'price' =>460 ,
                'stock_quantity' =>13 ,
                'description' => 'Fragrant pandan layers with coconut cream frosting.', 
                'sku' => 'fil102',
            ],
            [
                'name' => '. Dulce de Leche Cake',
                'category' => 'Caramel Cake',
                'price' => 575,
                'stock_quantity' =>12 ,
                'description' => 'Sweet and creamy vanilla cake filled with dulce de leche.', 
                'sku' => 'caram102',
            ],
            [
                'name' => 'Matcha Green Tea Cake',
                'category' => 'Specialty Cake',
                'price' => 525,
                'stock_quantity' =>9 ,
                'description' => 'Soft matcha sponge paired with light matcha cream.', 
                'sku' => 'spec102',
            ],
            [
                'name' => 'Salted Caramel Chocolate Cake',
                'category' => 'Chocolate Cake',
                'price' =>730 ,
                'stock_quantity' => 7,
                'description' => 'Rich chocolate cake with salted caramel layers.', 
                'sku' => 'choc105',
            ],
            [
                'name' => 'Blueberry Yogurt Cake',
                'category' => 'Fruit & Vegetable Cake',
                'price' => 410,
                'stock_quantity' => 10,
                'description' => 'Soft vanilla cake filled with tangy blueberry yogurt cream', 
                'sku' => 'fvg104',
            ],
            [
                'name' => 'Vanilla Buttercream Cake',
                'category' => 'Milk Cake',
                'price' =>645 ,
                'stock_quantity' => 10,
                'description' => 'Simple vanilla sponge with smooth buttercream frosting.', 
                'sku' => 'mil102',
                ],
            [
                'name' => 'Coffee Crumble Cake',
                'category' => 'Coffee Cake',
                'price' =>445,
                'stock_quantity' =>9 ,
                'description' => 'Mocha sponge topped with crunchy coffee crumble.', 
                'sku' => 'coff102',
            ],
            [
                'name' => 'Banoffee Cake',
                'category' => 'Specialty Cake',
                'price' => 680,
                'stock_quantity' => 8,
                'description' => 'Banana cake with caramel filling and whipped cream topping', 
                'sku' => 'spec103',
            ],
            [
                'name' => 'Chocolate Mousse Cake',
                'category' => 'Chocolate Cake',
                'price' =>590 ,
                'stock_quantity' =>12 ,
                'description' => 'Light and airy chocolate mousse layered with soft sponge.', 
                'sku' => 'choc106',
            ],
            [
                'name' => 'Strawberry Cheesecake',
                'category' => 'Cheese Cake',
                'price' => 595,
                'stock_quantity' =>14 ,
                'description' => 'Creamy cheesecake topped with fresh strawberry sauce', 
                'sku' => 'cheese103',
            ],
            [
                'name' => 'Special Card',
                'category' => 'Greeting Card',
                'price' =>20 ,
                'stock_quantity' => 15,                 
                'sku' => 'grt102',
            ],
            [
                'name' => 'Occasional Card',
                'category' => 'Greeting Card',
                'price' => 25,
                'stock_quantity' => 6, 
                'sku' => 'grt101',
            ],
            [
                'name' => 'Spark Candle',
                'category' => 'Candles',
                'price' => 35,
                'stock_quantity' =>7 ,
                'sku' => 'Scand102',
            ],
            [
                'name' => 'Plain Color Candle',
                'category' => 'Candles',
                'price' => 15,
                'stock_quantity' =>10 , 
                'sku' => 'cand103',
            ],
            [
                'name' => 'Spiral Candle',
                'category' => 'Candles',
                'price' => 18,
                'stock_quantity' =>11 , 
                'sku' => 'cand104',
            ],
            [
                'name' => 'Gold Candle',
                'category' => 'Candles',
                'price' => 35,
                'stock_quantity' => 10,
                'sku' => 'Ncand105',
            ],
            [
                'name' => 'Silver Candle',
                'category' => 'Candles',
                'price' => 35,
                'stock_quantity' => 9,
                'sku' => 'Ncand106', 
            ],
            [
                'name' => 'Rose Gold Candle',
                'category' => 'Candles',
                'price' => 35,
                'stock_quantity' => 9,
                'sku' => 'Ncand107',
            ],
            [
                'name' => 'Anniversarry Top',
                'category' => 'Cake Topper',
                'price' => 50,
                'stock_quantity' =>8 ,
                'sku' => 'top101',
            ],
            [
                'name' => 'Birthday Top',
                'category' => 'Cake Topper',
                'price' => 50,
                'stock_quantity' =>9 ,
                'sku' => 'top102', 
            ],
            [
                'name' => 'Congratulatory Top',
                'category' => 'Cake Topper',
                'price' => 50,
                'stock_quantity' =>12 ,
                'sku' => 'top103',
            ],
            [
                'name' => 'Christmas Top',
                'category' => 'Cake Topper',
                'price' => 55,
                'stock_quantity' => 12,
                'sku' => 'top104', 
            ],
            [
                'name' => 'Sweet Birthday',
                'category' => 'Cake Topper',
                'price' => 45,
                'stock_quantity' => 11,
                'sku' => 'top105',
            ],
        ];

        foreach ($products as $productData) {
            Product::create($productData);
        }
    }
}