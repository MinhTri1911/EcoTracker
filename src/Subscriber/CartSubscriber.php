<?php declare(strict_types=1);

namespace EcoTracker\Subscriber;

use EcoTracker\Services\ConfigService;
use Shopware\Storefront\Page\Checkout\Cart\CheckoutCartPageLoadedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

readonly class CartSubscriber implements EventSubscriberInterface
{

    public function __construct(private readonly ConfigService $configService)
    {

    }

    public static function getSubscribedEvents(): array
    {
        return [
            CheckoutCartPageLoadedEvent::class => ['onCartPageLoaded', 100]
        ];
    }

    public function onCartPageLoaded(CheckoutCartPageLoadedEvent $event): void
    {
        if (!$this->configService->isTrackAddToCartEnabled()) {
            return;
        }

        $cart = $event->getPage()->getCart();
        foreach ($cart->getLineItems() as $lineItem) {
            $cartData = [
                'event' => 'add_to_cart',
                'ecommerce' => [
                    'items' => [
                        [
                            'item_id' => $lineItem->getId(),
                            'item_name' => $lineItem->getLabel(),
                            'price' => $lineItem->getPrice(),
                            'quantity' => $lineItem->getQuantity(),
                        ]
                    ]
                ]
            ];

            echo "<script>window.dataLayer.push(" . json_encode($cartData) . ");</script>";
        }
    }
}

