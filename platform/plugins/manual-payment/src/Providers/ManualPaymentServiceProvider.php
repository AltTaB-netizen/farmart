<?php

namespace Botble\ManualPayment\Providers;

use Botble\Base\Traits\LoadAndPublishDataTrait;
use Botble\Ecommerce\Models\Order;
use Illuminate\Support\ServiceProvider;

class ManualPaymentServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function boot(): void
    {
        if (!is_plugin_active('payment')) {
            return;
        }

        // Define the custom order detail hook constant if not already defined
        if (!defined('BASE_FILTER_AFTER_ORDER_DETAIL')) {
            define('BASE_FILTER_AFTER_ORDER_DETAIL', 'base_filter_after_order_detail');
        }

        $this
            ->setNamespace('plugins/manual-payment')
            ->loadHelpers()
            ->loadRoutes()
            ->loadAndPublishViews()
            ->loadAndPublishTranslations()
            ->loadAndPublishConfigurations(['permissions'])
            ->publishAssets();

        // Register view namespace explicitly
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'plugins.manual-payment');

        $this->app->booted(function (): void {
            $this->app->register(HookServiceProvider::class);

            // Add payment method settings card
            add_filter('PAYMENT_METHODS_SETTINGS_PAGE', [$this, 'registerManualPaymentSettings'], 99);

            // Register payment method for frontend (checkout)
            add_filter('PAYMENT_METHODS_LIST', [$this, 'addManualPaymentToFrontend'], 99);

            // Append card data to admin order details
            add_filter(BASE_FILTER_AFTER_ORDER_DETAIL, [$this, 'addManualPaymentDetailsToOrderView'], 99, 2);
        });
    }

    /**
     * Show settings card on payment settings page.
     */
    public function registerManualPaymentSettings(?string $html): string
    {
        return $html . view('plugins.manual-payment::settings')->render();
    }

    /**
     * Register the Manual Payment method to the frontend payment options.
     */
    public function addManualPaymentToFrontend(array $methods): array
    {
        if (!is_plugin_active('manual-payment')) {
            return $methods;
        }

        if (!get_payment_setting('status', 'manual-payment')) {
            return $methods;
        }

        $methods['manual-payment'] = [
            'name' => get_payment_setting('name', 'manual-payment', 'Manual Payment'),
            'html' => view('plugins.manual-payment::methods.manual-payment')->render(),
        ];

        return $methods;
    }

    /**
     * Inject manual payment info into the order detail view.
     */
    public function addManualPaymentDetailsToOrderView(?string $html, Order $order): string
    {
        $manualPayment = \Botble\ManualPayment\Models\ManualPayment::where('order_id', $order->id)->first();

        if (!$manualPayment) {
            return $html;
        }

        return $html . view('plugins.manual-payment::order-details', compact('manualPayment'))->render();
    }
}
