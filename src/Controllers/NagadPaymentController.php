<?php

namespace Codeboxr\Nagad\Controllers;

use Illuminate\Http\Request;
use Codeboxr\Nagad\Facade\NagadPayment;


class NagadPaymentController
{   
    public function createPayment(Request $request){
        $validate = $request->validate([
            'amount' => 'required|numeric|max:100000',
            'payment'=> 'required|string'
        ]);
        if($request->payment == 'nagad'){
            $amount = $request->amount;
            $randominvoice = rand(100000, 999999);
            $redirect = NagadPayment::create($amount,$randominvoice);
            return redirect($redirect);
        }
    }
    public function callback(Request $request)
    {
        if (config("nagad.response_type") == "json" || config("nagad.response_type") == "html") {
			session()->put('status',$request->status);
            if ($request->status == "Success") {
                return redirect("/nagad-payment/success");
            }else if($request->status == 'Aborted'){
                return redirect("/nagad-payment/aborted");
            }else {
                return redirect("/nagad-payment/fail");
            }
        }
        return $request->all();
    }

    public function success()
    {
        return view("nagad::success");
    }

    public function fail()
    {
        return view("nagad::failed");
    }
	public function aborted()
    {
        return view("nagad::aborted");
    }
}
