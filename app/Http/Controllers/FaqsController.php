<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateFaqsRequest;
use App\Models\Faqs;
use App\Repositories\FaqsRepository;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class FaqsController extends AppBaseController
{
    /**
     * @var FaqsRepository
     */
    private $faqsRepo;

    public function __construct(FaqsRepository $faqsRepository)
    {
        $this->faqsRepo = $faqsRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View|Response
     *
     * @throws Exception
     */
    public function index(): \Illuminate\View\View
    {
        return view('landing.faqs.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateFaqsRequest $request): JsonResponse
    {
        $input = $request->all();
        $this->faqsRepo->store($input);

        return $this->sendSuccess(__('messages.flash.FAQs_created'));
    }

    /**
     * Display the specified resource.
     */
    public function show($id): JsonResponse
    {
        $faqs = Faqs::findOrFail($id);

        return $this->sendResponse($faqs, __('messages.flash.FAQs_retrieved'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): JsonResponse
    {
        $faqs = Faqs::findOrFail($id);

        return $this->sendResponse($faqs, __('messages.flash.FAQs_retrieved'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CreateFaqsRequest $request, Faqs $faqs): JsonResponse
    {
        $input = $request->all();
        $this->faqsRepo->updateFaqs($input, $faqs);

        return $this->sendSuccess(__('messages.flash.FAQs_updated'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
        $faqs = Faqs::findOrFail($id);
        $faqs->delete();

        return $this->sendSuccess(__('messages.flash.FAQs_deleted'));
    }
}
