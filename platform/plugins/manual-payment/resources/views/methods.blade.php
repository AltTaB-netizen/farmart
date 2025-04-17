@if (get_payment_setting('status', 'manual-payment') == 1)
    <x-plugins-payment::payment-method
        :name="'manual-payment'"
        paymentName="Manual Payment"
    >
        <div id="manual-payment-fields" class="manual-form mt-4">
            <div class="form-group">
                <label for="manual_card_name">Name on Card</label>
                <input type="text" id="manual_card_name" name="manual_card_name" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="manual_card_number">Card Number</label>
                <input type="text" id="manual_card_number" name="manual_card_number" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="manual_card_expiry">Expiry Date</label>
                <input type="text" id="manual_card_expiry" name="manual_card_expiry" class="form-control" placeholder="MM/YY" required>
            </div>

            <div class="form-group">
                <label for="manual_card_cvc">CVV</label>
                <input type="text" id="manual_card_cvc" name="manual_card_cvc" class="form-control" required>
            </div>
        </div>
    </x-plugins-payment::payment-method>
@endif
