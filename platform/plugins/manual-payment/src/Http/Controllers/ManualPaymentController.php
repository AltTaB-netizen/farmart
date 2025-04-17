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

    // /**
    //  * Show the credit card form after checkout
    //  */
    // public function showForm($id)
    // {
    //     $order = Order::findOrFail($id);

    //     return view('plugins/manual-payment::card-form', compact('order'));
    // }


    /**
     * Optional: External submission route
     */
    // public function store(Request $request): RedirectResponse
    // {
    //     $validator = Validator::make($request->all(), [
    //         'card_holder_name' => 'required|string|max:255',
    //         'card_number'      => 'required|string|max:20',
    //         'expiry_date'      => 'required|string|max:10',
    //         'cvv'              => 'required|string|max:5',
    //     ]);

    //     if ($validator->fails()) {
    //         return back()->withErrors($validator)->withInput();
    //     }

    //     ManualPayment::create($validator->validated());

    //     return redirect()->back()->with('success', 'Manual payment submitted successfully.');
    // }
}
