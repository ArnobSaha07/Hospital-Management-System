<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\User;
use App\Repositories\PaymentGatewayRepository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Redirect;
use Laracasts\Flash\Flash;

class PaymentGatewayController extends Controller
{
    /**
     * @var PaymentGatewayRepository
     */
    private $PaymentGatewayRepository;

    /**
     * SettingController constructor.
     */
    public function __construct(PaymentGatewayRepository $PaymentGatewayRepository)
    {
        $this->PaymentGatewayRepository = $PaymentGatewayRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index(): \Illuminate\View\View
    {
        $tenantId = User::findOrFail(getLoggedInUserId())->tenant_id;
        $setting = Setting::where('tenant_id', $tenantId)->pluck('value', 'key')->toArray();

        return view('settings.Credentials', compact('setting'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->PaymentGatewayRepository->PaymentGateway($request->all());
        Flash::success(__('messages.flash.payment_gateway_updated'));

        return Redirect::back();
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): Response
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): Response
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id): Response
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): Response
    {
        //
    }
}
