<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateLunchBreakRequest;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\LunchBreak;
use App\Models\User;
use App\Repositories\LunchBreakRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Flash;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LunchBreakController extends AppBaseController
{
    /** @var LunchBreakRepository */
    private $lunchBreakRepository;

    public function __construct(LunchBreakRepository $lunchBreakRepository)
    {
        $this->lunchBreakRepository = $lunchBreakRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        return view('lunch_breaks.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $doctor = Doctor::with('user')->get()->where('user.status', User::ACTIVE)->pluck('user.full_name','id');

        return view('lunch_breaks.create', compact('doctor'));
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector.
     */
    public function store(CreateLunchBreakRequest $request): RedirectResponse
    {
        $input = $request->all();
        $appointments = Appointment::whereDoctorId($input['doctor_id'])->get();

        foreach($appointments as $appointment){
            $time = Carbon::parse($appointment->opd_date)->format('h:i:s');
            $breakTime = Carbon::createFromTimeString($time)->between($input['break_from'],$input['break_to']);
            if($breakTime){
                Flash::error(__('messages.lunch_break.appointment_exist_time'));

                return redirect(route('breaks.create'));
            }
        }
        if(isset($input['date']) && !empty($input['date'])){
            $opdDates = Appointment::whereRaw('DATE(opd_date) = ?', $input['date'])->exists();

            if($opdDates){
                Flash::error(__('messages.lunch_break.appointment_exist_time'));

                return redirect(route('breaks.create'));
            }
        }
        $lunchBreak = $this->lunchBreakRepository->store($input);

        if ($lunchBreak) {
            Flash::success(__('messages.lunch_break.break_create'));

            return redirect(route('breaks.index'));
        } else {
            Flash::error(__('messages.lunch_break.break_already_is_exist'));

            return redirect(route('breaks.create'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        $checkRecord = LunchBreak::destroy($id);

        return $this->sendSuccess(__('Lunch Break deleted successfully.'));
    }
}
