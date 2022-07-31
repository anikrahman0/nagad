<?php

namespace Codeboxr\Nagad\Payment;

use Carbon\Carbon;
use Codeboxr\Nagad\Traits\Helpers;
use Illuminate\Support\Facades\Http;
use Codeboxr\Nagad\Exception\NagadException;

class Payment
{
    use Helpers;

    public string $base_url;

    public function __construct()
    {
        $this->baseUrl();
    }

    /**
     * Nagad Base Url
     * if sandbox is true it will be sandbox url otherwise it is host url
     */
    private function baseUrl()
    {
        if (config("nagad.sandbox") == true) {
            $this->base_url = 'http://sandbox.mynagad.com:10080/remote-payment-gateway-1.0/api/dfs/';
        } else {
            $this->base_url = 'https://api.mynagad.com/api/dfs/';
        }
    }

    /**
     * Nagad Request Headers
     *
     * @return array
     */
    private function headers()
    {
        return [
            "Content-Type"     => "application/json",
            "X-KM-IP-V4"       => $this->getIp(),
            "X-KM-Api-Version" => "v-0.2.0",
            "X-KM-Client-Type" => "PC_WEB"
        ];
    }

    /**
     * initialize payment
     *
     * @param $invoice
     *
     * @return mixed
     * @throws NagadException
     * @throws \Codeboxr\Nagad\Exception\InvalidPrivateKey
     * @throws \Codeboxr\Nagad\Exception\InvalidPublicKey
     */
    private function initPayment($invoice)
    {
        $baseUrl       = $this->base_url . "check-out/initialize/" . config("nagad.merchant_id") . "/{$invoice}";
        $sensitiveData = $this->getSensitiveData($invoice);
        $body          = [
            "accountNumber" => config("nagad.merchant_number"),
            "dateTime"      => Carbon::now()->timezone(config("timezone"))->format('YmdHis'),
            "sensitiveData" => $this->encryptWithPublicKey(json_encode($sensitiveData)),
            'signature'     => $this->signatureGenerate(json_encode($sensitiveData)),
        ];

        $response = Http::withHeaders($this->headers())->post($baseUrl, $body);
        $response = json_decode($response->body());

        if (isset($response->reason)) {
            throw new NagadException($response->message);
        }

        return $response;
    }

    /**
     * Redirect Nagad Payment Checkout Page
     *
     * @param $amount
     * @param $invoice
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws NagadException
     * @throws \Codeboxr\Nagad\Exception\InvalidPrivateKey
     * @throws \Codeboxr\Nagad\Exception\InvalidPublicKey
     */
    public function create($amount, $invoice)
    {
        $initialize = $this->initPayment($invoice);

        if ($initialize->sensitiveData && $initialize->signature) {
            $decryptData        = json_decode($this->decryptDataPrivateKey($initialize->sensitiveData));
            $url                = $this->base_url . "/check-out/complete/" . $decryptData->paymentReferenceId;
            $sensitiveOrderData = [
                'merchantId'   => config("nagad.merchant_id"),
                'orderId'      => $invoice,
                'currencyCode' => '050',
                'amount'       => $amount,
                'challenge'    => $decryptData->challenge
            ];

            $response = Http::withHeaders($this->headers())->post($url, [
                'sensitiveData'       => $this->encryptWithPublicKey(json_encode($sensitiveOrderData)),
                'signature'           => $this->signatureGenerate(json_encode($sensitiveOrderData)),
                'merchantCallbackURL' => config("nagad.callback_url"),
            ]);

            $response = json_decode($response->body());

            if (isset($response->reason)) {
                throw new NagadException($response->message);
            }

            if ($response->status == "Success") {
                return $response->callBackUrl;
            }
        }
    }

    /**
     * @param string $paymentRefId
     *
     * @return mixed
     */
    public function verify(string $paymentRefId)
    {
        $url      = $this->base_url . "verify/payment/{$paymentRefId}";
        $response = Http::withHeaders($this->headers())->get($url);
        return json_decode($response->body());
    }

}
