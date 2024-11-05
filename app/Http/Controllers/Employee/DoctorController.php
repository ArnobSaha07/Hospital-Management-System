<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\View\View;

class DoctorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Factory|View
     *
     * @throws Exception
     */
    public function index(Request $request): View
    {
        return view('employees.doctors.index');
    }

    /**
     * @return Factory|RedirectResponse|Redirector|View
     */
    public function show(int $id): View
    {
        $doctor = Doctor::findOrFail($id);

        return view('employees.doctors.show')->with('doctor', $doctor);
    }
}
