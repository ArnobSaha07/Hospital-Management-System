<?php

namespace App\Http\Controllers;

use App\Exports\EmployeePayrollExport;
use App\Http\Requests\CreateEmployeePayrollRequest;
use App\Http\Requests\UpdateEmployeePayrollRequest;
use App\Models\EmployeePayroll;
use App\Repositories\EmployeePayrollRepository;
use Exception;
use Flash;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class EmployeePayrollController extends AppBaseController
{
    /** @var EmployeePayrollRepository */
    private $employeePayrollRepository;

    public function __construct(EmployeePayrollRepository $employeePayrollRepo)
    {
        $this->employeePayrollRepository = $employeePayrollRepo;
    }

    /**
     * Display a listing of the EmployeePayroll.
     *
     * @return Factory|View
     *
     * @throws Exception
     */
    public function index(Request $request): View
    {
        $data['statusArr'] = EmployeePayroll::STATUS_ARR;

        return view('employee_payrolls.index', $data);
    }

    /**
     * Show the form for creating a new EmployeePayroll.
     *
     * @return Factory|View
     */
    public function create(): View
    {
        $srNo = EmployeePayroll::orderBy('id', 'desc')->value('id');
        $srNo = (! $srNo) ? 1 : $srNo + 1;
        $payrollId = strtoupper(Str::random(8));
        $types = EmployeePayroll::TYPES;
        asort($types);
        $months = EmployeePayroll::MONTHS;
        $status = EmployeePayroll::STATUS;

        return view('employee_payrolls.create', compact('srNo', 'payrollId', 'types', 'months', 'status'));
    }

    /**
     * Store a newly created EmployeePayroll in storage.
     *
     * @return RedirectResponse|Redirector
     */
    public function store(CreateEmployeePayrollRequest $request): RedirectResponse
    {
        $input = $request->all();
        $this->employeePayrollRepository->create($input);
        $this->employeePayrollRepository->createNotification($input);
        Flash::success(__('messages.flash.employee_payroll_saved'));

        return redirect(route('employee-payrolls.index'));
    }

    /**
     * @return Factory|RedirectResponse|Redirector|View
     */
    public function show(EmployeePayroll $employeePayroll)
    {
        if (! canAccessRecord(EmployeePayroll::class, $employeePayroll->id)) {
            Flash::error(__('messages.flash.not_allow_access_record'));

            return Redirect::back();
        }

        if (! getLoggedInUser()->hasRole('Admin')) {
            if ((getLoggedInUser()->owner_type != $employeePayroll->owner_type) || (getLoggedInUser()->owner_id != $employeePayroll->owner_id)) {
                return Redirect::back();
            }
        }

        return view('employee_payrolls.show')->with('employeePayroll', $employeePayroll);
    }

    /**
     * Show the form for editing the specified EmployeePayroll.
     *
     * @return Factory|View
     */
    public function edit(EmployeePayroll $employeePayroll)
    {
        if (! canAccessRecord(EmployeePayroll::class, $employeePayroll->id)) {
            Flash::error(__('messages.flash.not_allow_access_record'));

            return Redirect::back();
        }

        $types = EmployeePayroll::TYPES;
        $status = EmployeePayroll::STATUS;
        $employeePayroll->month = array_search($employeePayroll->month, EmployeePayroll::MONTHS);

        return view('employee_payrolls.edit', compact('employeePayroll', 'types', 'status'));
    }

    /**
     * Update the specified EmployeePayroll in storage.
     *
     * @return RedirectResponse|Redirector
     */
    public function update(EmployeePayroll $employeePayroll, UpdateEmployeePayrollRequest $request): RedirectResponse
    {
        $input = $request->all();
        $this->employeePayrollRepository->update($input, $employeePayroll->id);
        Flash::success(__('messages.flash.employee_payroll_updated'));

        return redirect(route('employee-payrolls.index'));
    }

    /**
     * @throws Exception
     */
    public function destroy(EmployeePayroll $employeePayroll): JsonResponse
    {
        if (! canAccessRecord(EmployeePayroll::class, $employeePayroll->id)) {
            return $this->sendError(__('messages.flash.employee_payroll_not_found'));
        }

        $employeePayroll->delete();

        return $this->sendSuccess('employee-payrolls.index');
    }

    public function getEmployeesList(Request $request): JsonResponse
    {
        if (empty($request->get('id'))) {
            return $this->sendError(__('messages.flash.employee_list_not_found'));
        }

        $employeesData = EmployeePayroll::CLASS_TYPES[$request->id]::with('user')
            ->get()->where('user.status', '=', 1)->pluck('user.full_name', 'id');

        return $this->sendResponse($employeesData, __('messages.flash.retrieve'));
    }

    public function employeePayrollExport(): BinaryFileResponse
    {
        $response = Excel::download(new EmployeePayrollExport, 'employee-payrolls-'.time().'.xlsx');

        ob_end_clean();

        return $response;
    }

    public function showModal(EmployeePayroll $employeePayroll): JsonResponse
    {
        if (! canAccessRecord(EmployeePayroll::class, $employeePayroll->id)) {
            return $this->sendError(__('messages.flash.employee_payroll_not_found'));
        }

        if ($employeePayroll->type_string == 'Doctor') {
            $employeePayroll->load(['owner.doctorUser']);
        } else {
            $employeePayroll->load(['owner.user']);
        }

        $employeePayroll = [
            'sr_no' => $employeePayroll->sr_no,
            'payroll_id' => $employeePayroll->payroll_id,
            'type_string' => $employeePayroll->type_string,
            'full_name' => $employeePayroll->owner->user->full_name,
            'month' => $employeePayroll->month,
            'year' => $employeePayroll->year,
            'basic_salary' => getCurrencyFormat($employeePayroll->basic_salary),
            'allowance' => getCurrencyFormat($employeePayroll->allowance),
            'deductions' => getCurrencyFormat($employeePayroll->deductions),
            'net_salary' => getCurrencyFormat($employeePayroll->net_salary),
            'status' => $employeePayroll->status,
            'created_at' => $employeePayroll->created_at,
            'updated_on' => $employeePayroll->updated_at,
        ];

        return $this->sendResponse($employeePayroll, __('messages.flash.employee_payroll_retrieved'));
    }
}
