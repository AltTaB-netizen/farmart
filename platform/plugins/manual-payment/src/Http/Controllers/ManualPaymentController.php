<?php

namespace Botble\ManualPayment\Http\Controllers;

use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Events\BeforeEditContentEvent;
use Botble\Ecommerce\Models\Order;
use Botble\ManualPayment\Models\ManualPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ManualPaymentController extends BaseController
{
    public function index(): View
    {
        $this->pageTitle(trans('plugins/manual-payment::manual-payment.name'));

        $payments = ManualPayment::latest()->paginate(10);

        return view('plugins/manual-payment::list', compact('payments'));
    }

    public function show($id): View
    {
        $payment = ManualPayment::findOrFail($id);

        event(new BeforeEditContentEvent(request(), $payment));

        return view('plugins/manual-payment::detail', compact('payment'));
    }

}
