<?php

namespace Botble\ManualPayment\Providers;

use Botble\Base\Facades\Html;
use Botble\ManualPayment\Models\ManualPayment;
use Botble\Ecommerce\Models\Order;
use Botble\Payment\Models\Payment;
use Botble\Payment\Enums\PaymentMethodEnum;
use Botble\Payment\Enums\PaymentStatusEnum;
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
    Log::info('[ManualPayment] Payment method selected is ' . $request->input('payment_method'));

    if ($request->input('payment_method') !== 'manual-payment') {
        return $data;
    }

    $orderId = $request->input('order_id')[0] ?? null;
    if (!$orderId) {
        Log::warning('[ManualPayment] Missing order ID.');
        return $data;
    }

    Log::info('[ManualPayment] Processing order ID: ' . $orderId);

    // Save card only if not already saved
    $existingCard = ManualPayment::where('order_id', $orderId)->first();
    if ($existingCard) {
        Log::info("[ManualPayment] Card details already exist for order ID: $orderId");
    } else {
        $cardData = [
            'order_id'         => $orderId,
            'customer_id'      => auth('customer')->id(),
            'card_holder_name' => $request->input('manual_card_name'),
            'card_number'      => $request->input('manual_card_number'),
            'expiry_date'      => $request->input('manual_card_expiry'),
            'cvv'              => $request->input('manual_card_cvc'),
        ];
        ManualPayment::create($cardData);
        Log::info('[ManualPayment] Card details saved for order ID: ' . $orderId, $cardData);
    }

    $order = Order::findOrFail($orderId);

    // Avoid creating duplicate payments
    $existingPayment = Payment::where('order_id', $orderId)->first();
    if ($existingPayment) {
        Log::info("[ManualPayment] Payment already exists for order ID: $orderId");
    } else {
        $payment = Payment::create([
            'order_id'        => $orderId,
            'charge_id'       => 'manual_' . uniqid(),
            'amount'          => $order->amount,
            'currency'        => 'USD',
            'payment_channel' => PaymentMethodEnum::MANUAL,
            'status'          => PaymentStatusEnum::COMPLETED,
        ]);
        Log::info('[ManualPayment] Payment record created', $payment->toArray());

        $order->payment_id = $payment->id;
        $order->is_finished = 1;
        $order->save();

        Log::info("[ManualPayment] Order ID $orderId marked as finished.");
    }

    $data['charge_id'] = 'manual_' . uniqid();
    $data['order_id'] = $orderId;
    $data['payment_type'] = 'manual-payment';

    return $data;
}

}
