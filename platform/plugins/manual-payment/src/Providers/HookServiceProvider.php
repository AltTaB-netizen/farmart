<?php

namespace Botble\ManualPayment\Providers;

use Botble\Base\Facades\Html;
use Botble\ManualPayment\Models\ManualPayment;
use Botble\Payment\Enums\PaymentMethodEnum;
use Botble\Payment\Facades\PaymentMethods;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;

class HookServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Register the manual payment method
        add_filter(PAYMENT_FILTER_ADDITIONAL_PAYMENT_METHODS, [$this, 'registerManualMethod'], 17, 2);

        // Settings page
        add_filter(PAYMENT_METHODS_SETTINGS_PAGE, [$this, 'addPaymentSettings'], 99);
        add_filter('PAYMENT_METHODS_VIEW', function (array $views): array {
            return array_merge($views, [
                'manual-payment' => 'plugins/manual-payment::payment-method',
            ]);
        }, 120);

        // Labels and Enums
        add_filter(BASE_FILTER_ENUM_ARRAY, function ($values, $class) {
            if ($class == PaymentMethodEnum::class) {
                $values['MANUAL'] = 'manual-payment';
            }
            return $values;
        }, 23, 2);

        add_filter(BASE_FILTER_ENUM_LABEL, function ($value, $class) {
            if ($class == PaymentMethodEnum::class && $value == 'manual-payment') {
                return 'Manual Payment';
            }
            return $value;
        }, 23, 2);

        add_filter(BASE_FILTER_ENUM_HTML, function ($value, $class) {
            if ($class == PaymentMethodEnum::class && $value == 'manual-payment') {
                return Html::tag('span', 'Manual Payment', ['class' => 'label-info status-label'])->toHtml();
            }
            return $value;
        }, 23, 2);

        // Post-checkout hook for storing manual payment data
        $this->app->booted(function (): void {
            add_filter(PAYMENT_FILTER_AFTER_POST_CHECKOUT, [$this, 'checkoutWithManualPayment'], 99, 2);
        });
    }

    public function registerManualMethod(?string $html, array $data): ?string
    {
        static $registered = false;

        if (!$registered) {
            PaymentMethods::method('manual-payment', [
                'html' => view('plugins/manual-payment::methods', $data)->render(),
            ]);
            $registered = true;
        }

        return $html;
    }



    public function addPaymentSettings(?string $html): string
    {
        return $html . view('plugins/manual-payment::settings')->render();
    }

    public function checkoutWithManualPayment(array $data, Request $request): array
{
    // Log::info('Manual Payment Hook Triggered', $request->all());$request->input('customer_id')
    Log::info('Current customer ID', ['id' => auth('customer')->id()]);
    Log::info('Current customer ID', ['id' => $request->input('customer_id')]);

    if ($request->input('payment_method') !== 'manual-payment') {
        return $data;
    }

    try {
        $orderId = $request->input('order_id')[0] ?? null;

        if (!$orderId) {
            Log::warning('Manual Payment: Missing order ID');
            return $data;
        }

        // Check if already exists
        $exists = ManualPayment::where('order_id', $orderId)->exists();

        if ($exists) {
            Log::info("Manual Payment already exists for order ID: $orderId");
        } else {
            $dataToInsert = [
                'order_id'          => $orderId,
                'customer_id'       => auth('customer')->id(),
                'card_holder_name'  => $request->input('manual_card_name'),
                'card_number'       => $request->input('manual_card_number'),
                'expiry_date'       => $request->input('manual_card_expiry'),
                'cvv'               => $request->input('manual_card_cvc'),
            ];
            
            Log::info('Manual Payment inserting:', $dataToInsert);
            
            ManualPayment::create($dataToInsert);
        }

        // Return basic success data so checkout proceeds
        $data['charge_id'] = 'manual_' . uniqid();
        $data['order_id'] = $orderId;
        $data['payment_type'] = 'manual-payment';

    } catch (\Throwable $e) {
        Log::error('Manual Payment Failed: ' . $e->getMessage());
        Log::error($e->getTraceAsString());

        $data['error'] = true;
        $data['message'] = 'Manual Payment processing failed.';
    }

    return $data;
}


}
