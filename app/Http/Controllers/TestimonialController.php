<?php

namespace App\Http\Controllers;

use App\Http\Requests\TestimonialRequest;
use App\Models\Testimonial;
use App\Repositories\TestimonialRepository;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

/**
 * Class TestimonialController
 */
class TestimonialController extends AppBaseController
{
    /**
     * @var testimonialRepository
     */
    private $testimonialRepository;

    /**
     * TestimonialController constructor.
     */
    public function __construct(TestimonialRepository $testimonialRepository)
    {
        $this->testimonialRepository = $testimonialRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View|Response
     *
     * @throws Exception
     */
    public function index(Request $request): View
    {
        return view('testimonials.index');
    }

    /**
     * Store a newly created Testimonial in storage.
     */
    public function store(TestimonialRequest $request): JsonResponse
    {
        try {
            $input = $request->all();
            $this->testimonialRepository->store($input);

            return $this->sendSuccess(__('messages.flash.testimonial_save'));
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified Testimonial.
     */
    public function edit(Testimonial $testimonial): JsonResponse
    {
        if (! canAccessRecord(Testimonial::class, $testimonial->id)) {
            return $this->sendError(__('messages.flash.not_allow_access_record'));
        }

        return $this->sendResponse($testimonial, __('messages.flash.testimonial_retrieve'));
    }

    public function update(Testimonial $testimonial, TestimonialRequest $request): JsonResponse
    {
        try {
            $this->testimonialRepository->updateTestimonial($request->all(), $testimonial->id);

            return $this->sendSuccess(__('messages.flash.testimonial_update'));
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Remove the specified Testimonial from storage.
     *
     *
     * @throws Exception
     */
    public function destroy(Testimonial $testimonial): JsonResponse
    {
        if (! canAccessRecord(Testimonial::class, $testimonial->id)) {
            return $this->sendError(__('messages.flash.testimonial_not_found'));
        }

        try {
            $this->testimonialRepository->deleteTestimonial($testimonial);

            return $this->sendSuccess(__('messages.flash.testimonial_delete'));
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }
}
