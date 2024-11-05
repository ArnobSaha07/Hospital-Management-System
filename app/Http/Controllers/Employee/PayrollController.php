<?php

namespace App\Http\Controllers\Employee;

use App\Exports\UserPayrollExport;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PayrollController extends Controller
{
    /**
     * @return Factory|View
     *
     * @throws Exception
     */
    public function index(Request $request): View
    {
        return view('employees.payrolls.index');
    }

    public function userPayrollExport(): BinaryFileResponse
    {
        $response = Excel::download(new UserPayrollExport, getLoggedInUser()->full_name.'-payroll-'.time().'.xlsx');

        ob_end_clean();

        return $response;
    }
}
