<?php

namespace App\payment;

use PayPalCheckoutSdk\Core\PayPalEnvironment;
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\ProductionEnvironment;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Orders\OrdersCaptureRequest;
use PayPalCheckoutSdk\Orders\OrdersCreateRequest;

class PaypalGateway
{
    private PayPalEnvironment $environment;

    private PayPalHttpClient $client;

    public function __construct()
    {
        $isSandbox = env('PAYPAL_SANDBOX_MODE') || false ;
        $clientId = env('PAYPAL_CLIENT_ID');
        $secret = env('PAYPAL_SECRET');


        $this->environment = $isSandbox ? new SandboxEnvironment($clientId,$secret) : new ProductionEnvironment($clientId,$secret) ;
        $this->client = new PayPalHttpClient( $this->environment );

    }


    public function createOrder($price,$info = "")
    {
        $request = new OrdersCreateRequest();
        $request->prefer('return=representation');

        $request->body =array(
            'intent' => 'CAPTURE',
            'application_context' =>
                array(
                    'brand_name' => 'ZAHI HOUSE',
                    'shipping_preference' => 'NO_SHIPPING',
                    'return_url' => 'http://f2i-cw1-ij-hc-nag.fr/',
                    'cancel_url' => 'http://f2i-cw1-ij-hc-nag.fr/',
                    'user_action'=> 'PAY_NOW'
                ),
            'purchase_units' =>
                array(
                    0 =>
                        array(
                            'soft_descripto'=>$info,
                            'amount' =>
                                array(
                                    'currency_code' => 'EUR',
                                    'value' => $price
                                )
                        ),
                ));

        $response= $this->client->execute($request);
        return ["success" => $response->statusCode==201 ,"orderID" => $response->result->id];

    }

    public function captureOrder(string $orderID): \PayPalHttp\HttpResponse
    {
        $request = new OrdersCaptureRequest($orderID);
        $request->prefer('return=representation');
        return $this->client->execute($request);

    }


}
