<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\CartEntry;
use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

final class CartController extends AbstractController
{
    #[Route('/cart', name: 'app_cart')]
    public function cart(Request $request, ProductRepository $productRepository) {
        $session = $request->getSession();

        /** @var Cart $cart */
        $cart = $session->get('cart', new Cart());

        $productRepository->fetchSessionProducts($cart);

        return $this->render('cart/index.html.twig', [
            'cart' => $cart,
        ]);
    }

    #[Route('/add-to-cart/{id}', name: 'app_add_to_cart')]
    public function add_to_cart(Product $product, Request $request): Response
    {
        $session = $request->getSession();

        /** @var Cart $cart */
        $cart = $session->get('cart', new Cart());
        $productEntry = null;
        foreach ($cart->getEntries() as $entry) {
            if ($entry->getProduct()->getId() === $product->getId()) {
                $productEntry = $entry;
                $entry->setQuantity($entry->getQuantity() + 1);
            }
        }

        if (null === $productEntry) {
            $productEntry = new CartEntry();
            $productEntry->setProduct($product)
                ->setQuantity(1);
            $cart->addEntry($productEntry);
        }

        $session->set('cart', $cart);

        return new Response();
    }

    #[Route('/remove-from-cart/{id}', name: 'app_remove_from_cart')]
    public function remove_from_cart(Product $product, Request $request): Response
    {
        $session = $request->getSession();

        /** @var Cart $cart */
        $cart = $session->get('cart', new Cart());

        $productEntry = null;

        foreach ($cart->getEntries() as $entry) {
            $productEntry = $entry;
            if ($entry->getProduct()->getId() === $product->getId()) {
                $cart->removeEntry($entry);
            }
        }

        if ($productEntry === null) {
            throw new \RuntimeException();
        }

        $session->set('cart', $cart);

        return new Response();
    }
}
