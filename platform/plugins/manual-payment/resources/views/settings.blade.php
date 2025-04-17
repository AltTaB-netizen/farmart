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
    {{-- Required Blade named slot: instructions --}}
    <x-slot name="instructions">
        <ol>
            <li>{{ __('Customers provide credit card information during checkout.') }}</li>
            <li>{{ __('You manually process the payment.') }}</li>
            <li>{{ __('Ensure all handling of card data complies with PCI-DSS standards.') }}</li>
        </ol>
    </x-slot>

    {{-- Required Blade named slot: fields --}}
    <x-slot name="fields">
        {{-- No API keys needed for manual method, leave this empty --}}
    </x-slot>
</x-plugins-payment::settings-card>
