<?php declare(strict_types=1);

namespace EcoTracker\Services;

use EcoTracker\EcoTracker;
use Shopware\Core\System\SystemConfig\SystemConfigService;

readonly class ConfigService
{
    public function __construct(private readonly SystemConfigService $systemConfigService)
    {
    }

    public function getGaTrackingId(): ?string
    {
        return $this->systemConfigService->get(EcoTracker::PLUGIN_NAME . '.config.gaTrackingId');
    }

    public function getGtmContainerId(): ?string
    {
        return $this->systemConfigService->get(EcoTracker::PLUGIN_NAME . '.config.gtmContainerId');
    }

    public function isTrackPurchaseEnabled(): bool
    {
        return (bool) $this->systemConfigService->get(EcoTracker::PLUGIN_NAME . '.config.trackPurchase');
    }

    public function isTrackAddToCartEnabled(): bool
    {
        return (bool) $this->systemConfigService->get(EcoTracker::PLUGIN_NAME . '.config.trackAddToCart');
    }

    public function isTrackProductConfigurationChangedEnabled(): bool
    {
        return (bool) $this->systemConfigService->get(EcoTracker::PLUGIN_NAME . '.config.trackProductConfigurationChanged');
    }
}

