<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    private const array DATA = [
        0 => [
            'name' => 'DOOGEE U11(2025) Android 15 Tablet with Keyboard,11 inch Android Tablet 16GB+128GB/2TB TF Octa-Core Gaming Tablets,90Hz Display,8580mAh,1080P,BT5.0+5G WiFi,13MP+5MP, Face ID,Metal Body',
            'image' => 'product_1.jpg',
            'price' => 1299.50,
            'quantity' => 30
        ],
        1 => [
            'name' => 'Android 14 Tablet 2025 Latest Tablets, 10 inch Tablet (4+4)GB RAM 64GB ROM 1TB Expand, 2 in 1 Tablets with Keyboard, Case, Stylus, Octa-Core Tableta PC, WiFi 6, Bluetooth, Dual Camera,7000mAh Battery',
            'image' => 'product_2.jpg',
            'price' => 999.99,
            'quantity' => 19
        ],
        2 => [
            'name' => 'Apple iPhone 14 Pro Max, 256GB, Deep Purple - Unlocked (Renewed)',
            'image' => 'product_3.jpg',
            'price' => 6289.35,
            'quantity' => 24
        ],
        3 => [
            'name' => 'SAMSUNG Galaxy S25 Edge Phone, 512 GB AI Smartphone, Unlocked Android, Night Video, Fast Processor, ProScaler Display, All-Day Battery, 2025, US 1 Yr Manufacturer Warranty, Titanium JetBlack',
            'image' => 'product_4.jpg',
            'price' => 819.85,
            'quantity' => 15
        ],
        4 => [
            'name' => 'Alienware 34 Curved Gaming Monitor – AW3425DWM - 34-inch WQHD 180Hz 1ms Display, 1500R, AMD FreeSync Premium, VESA AdaptiveSync.',
            'image' => 'product_5.jpg',
            'price' => 3199.65,
            'quantity' => 20
        ],
        5 => [
            'name' => 'SAMSUNG 27-inch Odyssey QD-OLED G8 (G81SF), 4K, 240Hz, Gaming Monitor, 0.03ms Response Time, DisplayHDR True Black 400, AMD FreeSync™ Premium Pro, Ergonomic Stand, LS27FG810SNXZA, 2025',
            'image' => 'product_6.jpg',
            'price' => 8999.45,
            'quantity' => 25
        ],
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::DATA as $productData) {
             $product = new Product();

             $product->setName($productData['name'])
                 ->setImage($productData['image'])
                 ->setPrice($productData['price'])
                 ->setQuantity($productData['quantity']);

             $manager->persist($product);
        }

        $manager->flush();
    }
}
