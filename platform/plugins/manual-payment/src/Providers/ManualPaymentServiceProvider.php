<?php

namespace Botble\ManualPayment\Providers;

use Botble\Base\Traits\LoadAndPublishDataTrait;
use Botble\Payment\Enums\PaymentMethodEnum;
use Illuminate\Support\ServiceProvider;

class ManualPaymentServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function boot(): void
    {
        if (!is_plugin_active('payment')) {
            return;
        }

        $this
            ->setNamespace('plugins/manual-payment')
            ->loadHelpers()
            ->loadRoutes()
            ->loadAndPublishViews()
            ->loadAndPublishTranslations()
            ->loadAndPublishConfigurations(['permissions'])
            ->publishAssets();

        $this->app->booted(function (): void {
            $this->app->register(HookServiceProvider::class);

            add_filter('PAYMENT_METHODS_SETTINGS_PAGE', [$this, 'registerManualPaymentSettings'], 99);
            add_filter('PAYMENT_METHODS_LIST', [$this, 'addManualPaymentToFrontend'], 99);
        });
    }

    public function registerManualPaymentSettings(?string $html): string
    {
        if (str_contains($html, 'id="manual-payment"')) {
            return $html;
        }

        return $html . view('plugins.manual-payment::settings')->render();
    }

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

}
