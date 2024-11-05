<?php

namespace App\Http\Controllers;

use App\Exports\MedicineExport;
use App\Http\Requests\CreateMedicineRequest;
use App\Http\Requests\UpdateMedicineRequest;
use App\Models\Medicine;
use App\Models\PurchasedMedicine;
use App\Models\SaleMedicine;
use App\Repositories\MedicineRepository;
use Exception;
use Flash;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class MedicineController extends AppBaseController
{
    /** @var MedicineRepository */
    private $medicineRepository;

    public function __construct(MedicineRepository $medicineRepo)
    {
        $this->medicineRepository = $medicineRepo;
    }

    /**
     * Display a listing of the Medicine.
     *
     * @return Factory|View|Response
     *
     * @throws Exception
     */
    public function index(Request $request): View
    {
        return view('medicines.index');
    }

    /**
     * Show the form for creating a new Medicine.
     *
     * @return Factory|View
     */
    public function create(): View
    {
        $data = $this->medicineRepository->getSyncList();

        return view('medicines.create')->with($data);
    }

    /**
     * Store a newly created Medicine in storage.
     *
     * @return RedirectResponse|Redirector
     */
    public function store(CreateMedicineRequest $request): RedirectResponse
    {
        $input = $request->all();
        $this->medicineRepository->create($input);
        Flash::success(__('messages.flash.medicine_saved'));

        return redirect(route('medicines.index'));
    }

    /**
     * Display the specified Medicine.
     *
     * @return Factory|View
     */
    public function show(Medicine $medicine)
    {
        if (! canAccessRecord(Medicine::class, $medicine->id)) {
            Flash::error(__('messages.flash.not_allow_access_record'));

            return Redirect::back();
        }

        $medicine->brand;
        $medicine->category;

        return view('medicines.show')->with('medicine', $medicine);
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|Factory|\Illuminate\Contracts\View\View|RedirectResponse
     */
    public function edit(Medicine $medicine)
    {
        if (! canAccessRecord(Medicine::class, $medicine->id)) {
            Flash::error(__('messages.flash.not_allow_access_record'));

            return Redirect::back();
        }

        $data = $this->medicineRepository->getSyncList();
        $data['medicine'] = $medicine;

        return view('medicines.edit')->with($data);
    }

    /**
     * Update the specified Medicine in storage.
     *
     * @return RedirectResponse|Redirector
     */
    public function update(Medicine $medicine, UpdateMedicineRequest $request): RedirectResponse
    {
        $this->medicineRepository->update($request->all(), $medicine->id);

        Flash::success(__('messages.flash.medicine_updated'));

        return redirect(route('medicines.index'));
    }

    /**
     * Remove the specified Medicine from storage.
     *
     *
     * @throws Exception
     */
    public function destroy(Medicine $medicine): JsonResponse
    {
        if (! canAccessRecord(Medicine::class, $medicine->id)) {
            return $this->sendError(__('messages.flash.medicine_not_found'));
        }
        $purchaseMedicine = PurchasedMedicine::whereMedicineId($medicine->id)->get();
        $saleMedicine = SaleMedicine::whereMedicineId($medicine->id)->get();
        if (isset($purchaseMedicine) && ! empty($purchaseMedicine)) {
            $purchaseMedicine->map->delete();
        }
        if (isset($saleMedicine) && ! empty($saleMedicine)) {
            $saleMedicine->map->delete();
        }

        $this->medicineRepository->delete($medicine->id);

        return $this->sendSuccess(__('messages.flash.medicine_deleted'));
    }

    public function medicineExport(): BinaryFileResponse
    {
        $response = Excel::download(new MedicineExport, 'medicines-'.time().'.xlsx');

        ob_end_clean();

        return $response;
    }

    public function showModal(Medicine $medicine): JsonResponse
    {
        if (! canAccessRecord(Medicine::class, $medicine->id)) {
            return $this->sendError(__('messages.flash.not_allow_access_record'));
        }

        $medicine->load(['brand', 'category']);

        return $this->sendResponse($medicine, __('messages.flash.medicine_retrieved'));
    }

    public function checkUseOfMedicine(Medicine $medicine)
    {

        $SaleModel = [
            SaleMedicine::class,
            PurchasedMedicine::class,
        ];
        $result['result'] = canDelete($SaleModel, 'medicine_id', $medicine->id);
        $result['id'] = $medicine->id;

        if ($result) {

            return $this->sendResponse($result, __('messages.new_change.medicine_bill_already_use'));
        }

        return $this->sendResponse($result, __('messages.new_change.not_in_use'));

    }
}
