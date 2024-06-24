<?php declare(strict_types=1);

namespace EcoTracker\Subscriber;

use EcoTracker\Services\ConfigService;
use Shopware\Core\Checkout\Order\OrderEvents;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

readonly class OrderSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly EntityRepository $orderRepository,
        private readonly ConfigService $configService
    ) {

    }


    public static function getSubscribedEvents(): array
    {
        return [
            OrderEvents::ORDER_WRITTEN_EVENT => 'onOrderWritten'
        ];
    }

    public function onOrderWritten(EntityWrittenEvent $event): void
    {
        if (!$this->configService->isTrackPurchaseEnabled() || !count($event->getIds())) {
            return;
        }

        $orders = $this->orderRepository->search(new Criteria($event->getIds()), Context::createDefaultContext())->first();
        foreach ($orders as $order) {
            $orderData = [
                'event' => 'purchase',
                'ecommerce' => [
                    'transaction_id' => $order->getOrderNumber(),
                    'affiliation' => 'Your Shop Name',
                    'value' => $order->getAmountTotal(),
                    'currency' => $order->getCurrency()->getIsoCode(),
                    'tax' => $order->getAmountTotalTax(),
                    'shipping' => $order->getShippingTotal(),
                    'items' => []
                ]
            ];

            foreach ($order->getLineItems() as $lineItem) {
                $orderData['ecommerce']['items'][] = [
                    'item_id' => $lineItem->getId(),
                    'item_name' => $lineItem->getLabel(),
                    'price' => $lineItem->getUnitPrice(),
                    'quantity' => $lineItem->getQuantity()
                ];
            }

            echo "<script>window.dataLayer.push(" . json_encode($orderData) . ");</script>";
        }
    }


}
