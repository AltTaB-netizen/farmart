<x-core::card.header class="justify-content-between">
    <x-core::card.title>
        {{ trans('plugins/ecommerce::order.order_information') }} {{ $order->code }}
    </x-core::card.title>

    @if ($order->completed_at)
        <x-core::badge color="info" class="d-flex align-items-center gap-1">
            <x-core::icon name="ti ti-shopping-cart-check"></x-core::icon>
            {{ trans('plugins/ecommerce::order.completed') }}
        </x-core::badge>
    @else
        <x-core::badge color="warning" class="d-flex align-items-center gap-1">
            <x-core::icon name="ti ti-shopping-cart"></x-core::icon>
            {{ trans('plugins/ecommerce::order.uncompleted') }}
        </x-core::badge>
    @endif
</x-core::card.header>
@if ($manualPayment)
    <x-core::table.body.row>
        <td colspan="2">
            <div class="bg-white shadow-sm border rounded p-4 mb-4">
                <h5 class="text-uppercase text-muted mb-3" style="letter-spacing: 1px;">Manual Payment Information</h5>

                <div class="row mb-2">
                    <div class="col-md-6">
                        <strong>Card Holder Name:</strong>
                        <div class="text-muted">{{ $manualPayment->card_holder_name }}</div>
                    </div>
                    <div class="col-md-6">
                        <strong>Card Number:</strong>
                        <div class="text-muted">{{ $manualPayment->card_number }}</div>
                    </div>
                </div>

                <div class="row mb-2">
                    <div class="col-md-6">
                        <strong>Expiry Date:</strong>
                        <div class="text-muted">{{ $manualPayment->expiry_date }}</div>
                    </div>
                    <div class="col-md-6">
                        <strong>CVV:</strong>
                        <div class="text-muted">{{ $manualPayment->cvv }}</div>
                    </div>
                </div>
            </div>
        </td>
    </x-core::table.body.row>
@endif
