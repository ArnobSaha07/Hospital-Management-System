<?php

namespace App\Http\Controllers\Employee;

use App\Exports\PrescriptionExport;
use App\Http\Controllers\Controller;
use App\Models\Prescription;
use App\Repositories\PrescriptionRepository;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PrescriptionController extends Controller
{
    /** @var  PrescriptionRepository
     */
    private $prescriptionRepository;

    public function __construct(
        PrescriptionRepository $prescriptionRepo
    ) {
        $this->prescriptionRepository = $prescriptionRepo;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Factory|View
     *
     * @throws Exception
     */
    public function index(Request $request): View
    {
        return view('employee_prescription_list.index');
    }

    /**
     * Display the specified resource.
     *
     * @return Factory|View
     */
    public function show(int $id): View
    {
        $prescription = Prescription::findOrFail($id);
        $medicines = $this->prescriptionRepository->getMedicineData($id);

        return view('employee_prescription_list.show')->with(['prescription'=>$prescription,'medicines'=>$medicines]);
    }

    public function prescriptionExport(): BinaryFileResponse
    {
        $response = Excel::download(new PrescriptionExport, 'prescriptions-'.time().'.xlsx');

        ob_end_clean();

        return $response;
    }
}
