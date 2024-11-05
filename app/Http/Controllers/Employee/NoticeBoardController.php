<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\AppBaseController;
use App\Models\NoticeBoard;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NoticeBoardController extends AppBaseController
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
        return view('employees.notice_boards.index');
    }

    /**
     * Display the specified resource.
     *
     * @return Application|Factory|\Illuminate\Contracts\View\View
     */
    public function show(int $id): View
    {
        $noticeBoard = NoticeBoard::findOrFail($id);

        return view('employees.notice_boards.show')->with('noticeBoard', $noticeBoard);
    }
}
