<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * Indicates whether the XSRF-TOKEN cookie should be set on the response.
     *
     * @var bool
     */
    protected $addHttpCookie = true;

    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        //
        'razorpay-payment-success',
        'razorpay-payment-failed',
        'patient-razorpay-payment-success',
        'patient-razorpay-payment-failed',
        'web-razorpay-payment-success',
        'appointment-razorpay-payment-success',
        'paytm-callback',
        'patient-paytm-callback',
        'ipd-phonepe-payment-success',
        'phonepe-payment-success',
        'medicine-purchase-razorpay-success',
        'medicine-purchase-paystack-payment-success',
        'purchase-medicine-phonepe-payment-success',
        'medicine-bill-razorpay-success',
        'medicine-bill-paystack-payment-success',
        'medicine-bill-phonepe-payment-success',
        'paystack-payment-success',
        'subscription-phonepe-payment-success',
        'appointment-phonepe-payment-success',
        'web-phonepe-payment-success',
    ];
}
