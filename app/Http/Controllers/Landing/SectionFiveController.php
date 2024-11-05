<?php

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\UpdateSectionFiveRequest;
use App\Models\SectionFive;
use App\Repositories\SectionFiveRepository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\App;
use Illuminate\View\View;

class SectionFiveController extends AppBaseController
{
    /**
     * @return Factory|View
     */
    public function index(): View
    {
        $sectionFive = SectionFive::first();

        return view('landing.section_five.index', compact('sectionFive'));
    }

    /**
     * @return Application|Factory|RedirectResponse|Redirector|View
     */
    public function update(UpdateSectionFiveRequest $request): RedirectResponse
    {
        /** @var SectionFiveRepository $repo */
        $repo = App::make(SectionFiveRepository::class);
        $repo->updateSectionFive($request->all());

        \Flash::success(__('messages.landing_cms.section_five').' '.__('messages.common.updated_successfully'));

        return redirect(route('super.admin.section.five'));
    }
}
