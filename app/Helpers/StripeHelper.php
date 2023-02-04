<?php

namespace App\Helpers;

use Stripe\Stripe;
use Stripe\Customer;
use Stripe\Charge;

/**
 *
 * Manage stripe customers
 *
 *
 *TESTING
 *$card = [
            'number' => '4242424242424242',
            'expiry-month' => '11',
            'expiry-year' => '17',
            'crypto' => '123'
        ];

        \App\Helpers\StripeHelper::createCustomer($card);

        \App\Helpers\StripeHelper::chargeCustomer('cus_Df8C7lng37LIAs', '10');
 *
 *
 *
 */
class StripeHelper
{
    public static function createCustomer($card, $customerId = null)
    {
        $stripe = Stripe::setApiKey(env('STRIPE_SECRET_KEY'));
        try {
            $token = \Stripe\Token::create([
                'card' => [
                    'number'    => $card['number'],
                    'exp_month' => $card['expiry-month'],
                    'exp_year'  => $card['expiry-year'],
                    'cvc'       => $card['crypto'],
                ],
            ]);

            if (isset($token['id'])) {
                if ($customerId != null) {
                    $customer = \Stripe\Customer::retrieve($customerId);
                    $customer->source = $token['id'];
                    $customer->save();
                } else {
                    $customer = Customer::create(array(
                        'source'  => $token['id'],
                    ));
                }

                return [
                    'result' => 'success',
                    'infos' => [
                        'cardId' => $token['card']->id,
                        'customerID' => $customer['id'],
                    ],
                ];
            }
        } catch (Stripe_CardError $e) {
            return [
                'result' => 'error',
                'error' => $e
            ];
        } catch (Stripe_InvalidRequestError $e) {
            // Invalid parameters were supplied to Stripe's API
            return [
                'result' => 'error',
                'error' => $e
            ];
        } catch (Stripe_AuthenticationError $e) {
            // Authentication with Stripe's API failed
            return [
                'result' => 'error',
                'error' => $e
            ];
        } catch (Stripe_ApiConnectionError $e) {
            // Network communication with Stripe failed
            return [
                'result' => 'error',
                'error' => $e
            ];
        } catch (Stripe_Error $e) {
            // Display a very generic error to the user, and maybe send
            // yourself an email
            return [
                'result' => 'error',
                'error' => $e
            ];
        } catch (Exception $e) {
            // Something else happened, completely unrelated to Stripe
            return [
                'result' => 'error',
                'error' => $e
            ];
        }
    }

    /*
     * charge customer
     */
    public static function chargeCustomer($customer, $amount, $currency = 'eur')
    {
        $stripe = Stripe::setApiKey(env('STRIPE_SECRET_KEY'));

        $charge = Charge::create(array(
            'customer' => $customer,
            'amount'   => $amount*100,
            'currency' => $currency
        ));

        dump($charge);
    }
}
