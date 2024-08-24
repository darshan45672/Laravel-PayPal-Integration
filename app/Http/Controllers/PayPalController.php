<?php

namespace App\Http\Controllers;

use App\Models\Payments;
use Illuminate\Http\Request;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class PayPalController extends Controller
{
    public function createPayment(Request $request)
    {
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();
        $response = $provider->createOrder([
            "intent" => "CAPTURE",
            "application_context" => [
                "return_url" => route('success'),
                "cancel_url" => route('cancel'),
            ],
            "purchase_units" => [
                [
                    "amount" => [
                        "currency_code" => "USD",
                        "value" => "100.00",
                    ]
                ]
            ]
        ]);

        // dd($response);
        if (isset($response['id']) && $response['id'] != null) {
            foreach ($response['links'] as $link) {
                if ($link['rel'] == 'approve') {
                    session()->put('product_name', $request->product_name);
                    session()->put('quantity', $request->quantity);
                    return redirect()->away($link['href']);
                }
            }
        } else {
            return redirect()->route('cancel');
        }
        // return $provider;
    }

    public function success(Request $request)
    {
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $token = $provider->getAccessToken();
        $response = $provider->capturePaymentOrder($request->token);

        if ( isset($response['status']) && $response['status'] == 'COMPLETED') {
            $payment = new Payments;
            $payment->payment_id = $response['id'];
            $payment->payer_name = $response['payer']['name']['given_name'];
            $payment->payer_email = $response['payer']['email_address'];
            $payment->product_name = session()->get('product_name');
            $payment->quantity = session()->get('quantity');
            $payment->amount = $response['purchase_units'][0]['payments']['captures'][0]['amount']['value'];
            $payment->currency = $response['purchase_units'][0]['payments']['captures'][0]['amount']['currency_code'];
            $payment->payment_status = $response['status'];
            $payment->payment_method = $response['payer']['payment_method'];
            $payment->save();

            unset($_SESSION['product_name']);
            unset($_SESSION['quantity']);
            
            return redirect()->route('home')->with('success', 'Payment completed successfully');
        }else{
            return redirect()->route('cancel');
        }
    }

    public function cancel(Request $request)
    {
        // dd($request->all());
    }
}
