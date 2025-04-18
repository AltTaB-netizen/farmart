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
