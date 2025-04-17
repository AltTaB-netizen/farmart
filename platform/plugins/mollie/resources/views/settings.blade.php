@php
    if (!defined('MANUAL_PAYMENT_METHOD_NAME')) {
        define('MANUAL_PAYMENT_METHOD_NAME', 'manual-payment');
    }
@endphp

<x-plugins-payment::settings-card
    name="Manual Payment"
    :id="MANUAL_PAYMENT_METHOD_NAME"
    :logo="asset('storage/manual-payment-logo.png')"
    url="#"
    :description="__('Customers can pay manually using credit card. You will process it manually.')"
>
    <x-slot name="instructions">
        <ol>
            <li>
                <p>{{ __('Customers will enter their credit card information during checkout.') }}</p>
            </li>
            <li>
                <p>{{ __('You will manually process the payment using your own terminal or service.') }}</p>
            </li>
            <li>
                <p>{{ __('Ensure secure storage and handling of customer data.') }}</p>
            </li>
        </ol>
    </x-slot>

    <x-slot name="fields">
        {{-- No fields required for manual processing --}}
    </x-slot>
</x-plugins-payment::settings-card>
