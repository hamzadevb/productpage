<?php

namespace App\Repository;

use App\Entity\Cart;
use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function fetchSessionProducts(Cart $cart) {
        $entries = $cart->getEntries();
        $productsIds = [];
        foreach ($entries as $entry) {
            $productsIds[] = $entry->getProduct()->getId();
        }
        $productsIds = array_unique($productsIds);

        return $this->createQueryBuilder('p')
            ->where('p.id IN (:products)')
            ->setParameter('products', $productsIds)
            ->getQuery()
            ->getResult();
    }
}
