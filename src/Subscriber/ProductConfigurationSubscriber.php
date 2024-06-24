<?php declare(strict_types=1);

namespace EcoTracker\Subscriber;

use EcoTracker\Services\ConfigService;
use Shopware\Storefront\Page\Product\ProductPageLoadedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

readonly class ProductConfigurationSubscriber implements EventSubscriberInterface
{

    public function __construct(private ConfigService $configService)
    {}

    public static function getSubscribedEvents(): array
    {
        return [
            ProductPageLoadedEvent::class => 'onProductPageLoaded'
        ];

    }

    public function onProductPageLoaded(ProductPageLoadedEvent $event): void
    {
        if (!$this->configService->isTrackProductConfigurationChangedEnabled()) {
            return;
        }

        $product = $event->getPage()->getProduct();
        $productData = [
            'event' => 'product_configuration_changed',
            'ecommerce' => [
                'items' => [
                    [
                        'item_id' => $product->getId(),
                        'item_name' => $product->getName(),
                        'price' => $product->getPrice()->first()->getGross(),
                        'configuration_option' => $product->getVariation(),
                        'configuration_value' => $product->getVariation()
                    ]
                ]
            ]
        ];

        echo "<script>window.dataLayer.push(" . json_encode($productData) . ");</script>";
    }
}
