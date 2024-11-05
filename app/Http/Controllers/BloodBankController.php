<?php

namespace App\Http\Controllers;

use App\Exports\BloodBankExport;
use App\Http\Requests\CreateBloodBankRequest;
use App\Http\Requests\UpdateBloodBankRequest;
use App\Models\BloodBank;
use App\Models\BloodDonor;
use App\Models\User;
use App\Repositories\BloodBankRepository;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class BloodBankController extends AppBaseController
{
    /** @var BloodBankRepository */
    private $bloodBankRepository;

    public function __construct(BloodBankRepository $bloodBankRepo)
    {
        $this->middleware('check_menu_access');
        $this->bloodBankRepository = $bloodBankRepo;
    }

    /**
     * Display a listing of the BloodBank.
     *
     * @return Factory|View
     *
     * @throws Exception
     */
    public function index(Request $request): View
    {
        return view('blood_banks.index');
    }

    /**
     * Store a newly created BloodBank in storage.
     */
    public function store(CreateBloodBankRequest $request): JsonResponse
    {
        $input = $request->all();
        $this->bloodBankRepository->create($input);

        return $this->sendSuccess(__('messages.flash.blood_group_saved'));
    }

    /**
     * Show the form for editing the specified BloodBank.
     */
    public function edit(BloodBank $bloodBank): JsonResponse
    {
        if (! canAccessRecord(BloodBank::class, $bloodBank->id)) {
            return $this->sendError(__('messages.flash.not_allow_access_record'));
        }

        return $this->sendResponse($bloodBank, __('messages.flash.blood_bank_retrieved'));
    }

    /**
     * Update the specified BloodBank in storage.
     */
    public function update(BloodBank $bloodBank, UpdateBloodBankRequest $request): JsonResponse
    {
        $input = $request->all();
        $this->bloodBankRepository->update($input, $bloodBank->id);

        return $this->sendSuccess(__('messages.flash.blood_group_updated'));
    }

    /**
     * Remove the specified BloodBank from storage.
     *
     *
     * @throws Exception
     */
    public function destroy(BloodBank $bloodBank): JsonResponse
    {
        if (! canAccessRecord(BloodBank::class, $bloodBank->id)) {
            return $this->sendError(__('messages.flash.blood_bank_not_found'));
        }

        $bloodBankModel = [
            BloodDonor::class, User::class,
        ];
        $result = canDelete($bloodBankModel, 'blood_group', $bloodBank->blood_group);
        if ($result) {
            return $this->sendError(__('messages.flash.blood_bank_cant_deleted'));
        }
        $bloodBank->delete($bloodBank->id);

        return $this->sendSuccess(__('messages.flash.blood_bank_deleted'));
    }

    public function bloodBankExport(): BinaryFileResponse
    {
        $response = Excel::download(new BloodBankExport, 'blood-banks-'.time().'.xlsx');

        ob_end_clean();

        return $response;
    }
}
