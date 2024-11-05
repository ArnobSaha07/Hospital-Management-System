<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateDocumentTypeRequest;
use App\Http\Requests\UpdateDocumentTypeRequest;
use App\Models\Document;
use App\Models\DocumentType;
use App\Repositories\DocumentTypeRepository;
use Exception;
use Flash;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class DocumentTypeController extends AppBaseController
{
    /** @var DocumentTypeRepository */
    private $documentTypeRepository;

    public function __construct(DocumentTypeRepository $documentTypeRepo)
    {
        $this->middleware('check_menu_access');
        $this->documentTypeRepository = $documentTypeRepo;
    }

    /**
     * Display a listing of the DocumentType.
     *
     * @return Factory|View
     *
     * @throws Exception
     */
    public function index(Request $request): View
    {
        return view('document_types.index');
    }

    /**
     * Store a newly created DocumentType in storage.
     */
    public function store(CreateDocumentTypeRequest $request): JsonResponse
    {
        $input = $request->all();

        $this->documentTypeRepository->create($input);

        return $this->sendSuccess(__('messages.flash.document_type_saved'));
    }

    /**
     * @return Application|Factory|\Illuminate\Contracts\View\View|RedirectResponse
     */
    public function show(DocumentType $documentType)
    {
        if (! canAccessRecord(DocumentType::class, $documentType->id)) {
            Flash::error(__('messages.flash.not_allow_access_record'));

            return Redirect::back();
        }

        $documents = $documentType->documents;
        if (! getLoggedInUser()->hasRole('Admin')) {
            $documents = Document::whereUploadedBy(getLoggedInUser()->id)->whereDocumentTypeId($documentType->id)->get();
        }

        return view('document_types.show', compact('documentType', 'documents'));
    }

    /**
     * Show the form for editing the specified DocumentType.
     */
    public function edit(DocumentType $documentType): JsonResponse
    {
        if (! canAccessRecord(DocumentType::class, $documentType->id)) {
            return $this->sendError(__('messages.flash.not_allow_access_record'));
        }

        return $this->sendResponse($documentType, __('messages.flash.document_type_retrieved'));
    }

    /**
     * Update the specified DocumentType in storage.
     */
    public function update(DocumentType $documentType, UpdateDocumentTypeRequest $request): JsonResponse
    {
        $this->documentTypeRepository->update($request->all(), $documentType->id);

        return $this->sendSuccess(__('messages.flash.document_type_updated'));
    }

    /**
     * Remove the specified DocumentType from storage.
     *
     *
     * @throws Exception
     */
    public function destroy(DocumentType $documentType): JsonResponse
    {
        if (! canAccessRecord(DocumentType::class, $documentType->id)) {
            return $this->sendError(__('messages.flash.document_type_not_found'));
        }

        $documentTypeModel = [
            Document::class,
        ];
        $result = canDelete($documentTypeModel, 'document_type_id', $documentType->id);
        if ($result) {
            return $this->sendError(__('messages.flash.document_type_cant_deleted'));
        }
        $this->documentTypeRepository->delete($documentType->id);

        return $this->sendSuccess(__('messages.flash.document_type_deleted'));
    }
}
