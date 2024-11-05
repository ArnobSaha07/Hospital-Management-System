<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Models\NoticeBoard;

class NoticeboardAPIController extends AppBaseController
{
    public function index(): \Illuminate\Http\JsonResponse
    {
        $noticeboards = NoticeBoard::where('tenant_id',getLoggedInUser()->tenant_id)->orderBy('id', 'desc')->get();

        $data = [];
        foreach ($noticeboards as $noticeboard) {
            $data[] = $noticeboard->prepareNoticeboardData();
        }

        return $this->sendResponse($data, 'Noticeboard Retrieved Successfully');
    }

    public function show($id): \Illuminate\Http\JsonResponse
    {
        $noticeboard = NoticeBoard::where('id', $id)->where('tenant_id',getLoggedInUser()->tenant_id)->select(['title', 'description'])->first();

        return $this->sendResponse($noticeboard, 'Noticeboard Retrieved Successfully');
    }
}
