<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Repository\CartRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Firebase\JWT\JWT;

final class PaymentController extends AbstractController
{
    #[Route('/initiate/payment', name: 'app_payment_initiate', methods: ['POST'])]
    public function initiate_payment(
        Request $request,
        EntityManagerInterface $entityManager,
        HttpClientInterface $client,
        ProductRepository $productRepository,
    ): Response {
        $session = $request->getSession();
        /** @var Cart $cart */
        $cart = $session->get('cart');
        if ($cart === null || $cart->getEntries()->count() === 0) {
            throw new BadRequestHttpException();
        }

        foreach ($cart->getEntries() as $entry) {
            $product = $productRepository->find($entry->getProduct()->getId());
            $entry->setProduct($product);
        }

        $entityManager->persist($cart);
        $entityManager->flush();

        $paymentData = [
            'orderId' => $cart->getId(),
            'amount' => $cart->getTotal(),
        ];

        $apiKey = $this->getParameter('apiKey');
        $apiSecret = $this->getParameter('apiSecret');

        $signature = JWT::encode($paymentData, $apiSecret, 'HS256');

        $response = $client->request('POST', 'http://smartep-app/payment/initiate', [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => "Bearer {$apiKey}",
                'X-Signature' => $signature,
            ],
            'body' => json_encode($paymentData)
        ]);

        if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
            $content = $response->toArray();

            return $this->json([
                'checkoutUrl' => 'http://localhost'.$content['checkoutUrl'],
            ]);
        } else {
            throw new \RuntimeException();
        }
    }

    #[Route('/payment/webhook', name: 'app_payment_webhook', methods: ['POST'])]
    public function webhook(
        Request $request,
        EntityManagerInterface $entityManager,
        CartRepository $cartRepository,
    ): Response {
        $authorization = $request->headers->get('Authorization');

        $apiKey = $this->getParameter('apiKey');
        $apiSecret = $this->getParameter('apiSecret');

        if ($authorization !== "Bearer {$apiKey}") {
            throw new NotFoundHttpException();
        }

        $payload = $request->getPayload()->all();

        $signature = JWT::encode($payload, $apiSecret, 'HS256');

        if ($signature !== $request->headers->get('X-Signature')) {
            throw new BadRequestHttpException();
        }

        $orderId = $payload['orderId'];
        $status = $payload['status'];
        $transaction = $payload['transaction'];

        $cart = $cartRepository->find($orderId);
        $cart->setStatus($status)
            ->setTransaction($transaction);

        $entityManager->flush();

        return new Response();
    }

    #[Route('/payment/result', name: 'app_payment_result', methods: ['GET'])]
    public function result(
        Request $request,
        CartRepository $cartRepository,
    ): Response {
        $session = $request->getSession();
        /** @var Cart $cart */
        $cart = $session->get('cart');

        /** @var Cart $cart */
        $cart = $cartRepository->find($cart->getId());

        if ($cart->getStatus() === true) {
            $session->remove('cart');
        }

        return $this->render('payment/result.html.twig', [
            'cart' => $cart,
        ]);
    }
}
