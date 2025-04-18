@php
    if (!defined('MANUAL_PAYMENT_METHOD_NAME')) {
        define('MANUAL_PAYMENT_METHOD_NAME', 'manual-payment');
    }
@endphp
@php
    $customLogo = setting('payment_manual-payment_logo');
    $defaultLogo = asset('vendor/core/manual-payment/images/default-logo.png'); // ← your actual path
    $logo = $customLogo && Storage::disk('public')->exists($customLogo) ? Storage::url($customLogo) : $defaultLogo;
@endphp

<x-plugins-payment::settings-card
    name="Manual Payment"
    :id="MANUAL_PAYMENT_METHOD_NAME"
    :logo="$logo"
    url="#"
    :description="__('Customers can pay manually using credit card. You will process it manually.')"
>
   <x-slot name="instructions">
        <ol>
            <li>{{ __('Customers provide credit card information during checkout.') }}</li>
            <li>{{ __('You manually process the payment.') }}</li>
            <li>{{ __('Ensure all handling of card data complies with PCI-DSS standards.') }}</li>
        </ol>
    </x-slot>

    <x-slot name="fields">
        {{-- No API keys needed for manual method, leave this empty --}}
    </x-slot>
</x-plugins-payment::settings-card>
