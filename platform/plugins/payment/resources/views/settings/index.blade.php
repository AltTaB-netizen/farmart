@php
    use Botble\Payment\Enums\PaymentMethodEnum;
    use Botble\Payment\Models\Payment;
@endphp

@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    {!! $form->renderForm() !!}

    @php
        do_action(BASE_ACTION_META_BOXES, 'top', new Payment);
    @endphp

    <div class="my-5 d-block d-md-flex">
        <div class="col-12 col-md-3"></div>
        <div class="col-12 col-md-9">
            {!! apply_filters(PAYMENT_METHODS_SETTINGS_PAGE, null) !!}

            <x-core::card class="mb-3">
    <x-core::table :hover="false" :striped="false">
        <x-core::table.body>
            <x-core::table.body.row>
                <x-core::table.body.cell class="border-end">
                    <x-core::icon name="ti ti-wallet" />
                </x-core::table.body.cell>
                <x-core::table.body.cell style="width: 20%">
                    {{ trans('plugins/payment::payment.payment_methods') }}
                </x-core::table.body.cell>
                <x-core::table.body.cell>
                    <p class="mb-0">{{ trans('plugins/payment::payment.payment_methods_instruction') }}</p>
                </x-core::table.body.cell>
            </x-core::table.body.row>
        </x-core::table.body>

        {{-- Render all available payment methods --}}
        @foreach(apply_filters('PAYMENT_METHODS', []) as $key => $method)
            @php
                $status = get_payment_setting('status', $key);
                $methodName = get_payment_setting('name', $key, $method['name'] ?? ucfirst(str_replace('_', ' ', $key)));
            @endphp

            <x-core::table.body>
                <x-core::table.body.row>
                    <x-core::table.body.cell colspan="3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="payment-name-label-group">
                                    @if($status)
                                        {{ trans('plugins/payment::payment.use') }}
                                    @endif
                                    <span class="method-name-label">{{ $methodName }}</span>
                                </div>
                            </div>

                            <x-core::button @class(['toggle-payment-item edit-payment-item-btn-trigger', 'hidden' => !$status])>
                                {{ trans('plugins/payment::payment.edit') }}
                            </x-core::button>
                            <x-core::button @class(['toggle-payment-item save-payment-item-btn-trigger', 'hidden' => $status])>
                                {{ trans('plugins/payment::payment.settings') }}
                            </x-core::button>
                        </div>
                    </x-core::table.body.cell>
                </x-core::table.body.row>
                <x-core::table.body.row class="payment-content-item hidden">
                    <x-core::table.body.cell colspan="3">
                        <x-core::form>
                        @php
    $status = get_payment_setting('status', $key);
    $methodName = get_payment_setting('name', $key, isset($method['name']) ? $method['name'] : ucfirst(str_replace('_', ' ', $key)));
@endphp


                            <div class="btn-list justify-content-end">
                                <x-core::button
                                    type="button"
                                    @class(['disable-payment-item', 'hidden' => !$status])
                                >
                                    {{ trans('plugins/payment::payment.deactivate') }}
                                </x-core::button>

                                <x-core::button
                                    @class(['save-payment-item btn-text-trigger-save', 'hidden' => $status])
                                    type="button"
                                    color="info"
                                >
                                    {{ trans('plugins/payment::payment.activate') }}
                                </x-core::button>
                                <x-core::button
                                    type="button"
                                    color="info"
                                    @class(['save-payment-item btn-text-trigger-update', 'hidden' => !$status])
                                >
                                    {{ trans('plugins/payment::payment.update') }}
                                </x-core::button>
                            </div>
                        </x-core::form>
                    </x-core::table.body.cell>
                </x-core::table.body.row>
            </x-core::table.body>
        @endforeach
    </x-core::table>
</x-core::card>

        </div>
    </div>

    @php
        do_action(BASE_ACTION_META_BOXES, 'main', new Payment);
    @endphp

    <div class="row">
        <div class="col-md-9 offset-col-md-3">
            @php
                do_action(BASE_ACTION_META_BOXES, 'advanced', new Payment);
            @endphp
        </div>
    </div>

    {!! apply_filters('payment_method_after_settings', null) !!}
@endsection

@push('footer')
    <x-core::modal.action
        id="confirm-disable-payment-method-modal"
        :title="trans('plugins/payment::payment.deactivate_payment_method')"
        :description="trans('plugins/payment::payment.deactivate_payment_method_description')"
        :submit-button-attrs="['id' => 'confirm-disable-payment-method-button']"
        :submit-button-label="trans('plugins/payment::payment.agree')"
    />
@endpush
