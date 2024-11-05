<?php

use App\Http\Controllers\Landing;
use App\Http\Controllers\SuperAdminEnquiryController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware('xss', 'languageChangeName')->group(function () {
    Route::get('/', [Landing\LandingScreenController::class, 'index'])->name('landing-home');
    Route::get('/about-us', [Landing\LandingScreenController::class, 'aboutUs'])->name('landing.about.us');
    Route::get('/our-services', [Landing\LandingScreenController::class, 'services'])->name('landing.services');
    Route::get('/pricing', [Landing\LandingScreenController::class, 'pricing'])->name('landing.pricing');
    Route::get('/contact-us', [Landing\LandingScreenController::class, 'contactUs'])->name('landing.contact.us');
    Route::get('/faqs', [Landing\LandingScreenController::class, 'faq'])->name('landing.faq');
    Route::get('/hospitals', [Landing\LandingScreenController::class, 'hospitals'])->name('landing.hospitals');
    Route::post('/subscribe', [Landing\SubscribeController::class, 'store'])->name('subscribe.store');
    Route::post('/enquiries', [SuperAdminEnquiryController::class, 'store'])->name('super.admin.enquiry.store');
});
