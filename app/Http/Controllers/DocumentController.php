<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateDocumentRequest;
use App\Http\Requests\UpdateDocumentRequest;
use App\Models\Document;
use App\Repositories\DocumentRepository;
use Exception;
use Flash;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Storage;
use Str;

class DocumentController extends AppBaseController
{
    /** @var DocumentRepository */
    private $documentRepository;

    public function __construct(DocumentRepository $documentRepo)
    {
        $this->documentRepository = $documentRepo;
    }

    /**
     * Display a listing of the Document.
     *
     * @return Factory|View
     *
     * @throws Exception
     */
    public function index(Request $request): View
    {
        $data = $this->documentRepository->getSyncList();

        return view('documents.index')->with($data);
    }

    /**
     * Store a newly created Document in storage.
     */
    public function store(CreateDocumentRequest $request): JsonResponse
    {
        $input = $request->all();

        $this->documentRepository->store($input);

        return $this->sendSuccess(__('messages.flash.document_saved'));
    }

    /**
     * @return Application|Factory|View
     */
    public function show(Document $document)
    {
        if (! canAccessRecord(Document::class, $document->id)) {
            Flash::error(__('messages.flash.not_allow_access_record'));

            return Redirect::back();
        }

        $documents = $this->documentRepository->find($document->id);
        $data = $this->documentRepository->getSyncList();
        $data['documents'] = $documents;

        return view('documents.show')->with($data);
    }

    /**
     * Show the form for editing the specified Document.
     */
    public function edit(Document $document): JsonResponse
    {
        if (! canAccessRecord(Document::class, $document->id)) {
            return $this->sendError(__('messages.flash.not_allow_access_record'));
        }

        if (getLoggedInUser()->hasRole('Patient')) {
            if (getLoggedInUser()->owner_id != $document->patient_id) {
                return $this->sendError(__('messages.flash.document_not_found'));
            }
        }

        return $this->sendResponse($document, __('messages.flash.document_retrieved'));
    }

    /**
     * Update the specified Document in storage.
     */
    public function update(Document $document, UpdateDocumentRequest $request): JsonResponse
    {
        $this->documentRepository->updateDocument($request->all(), $document->id);

        return $this->sendSuccess(__('messages.flash.document_updated'));
    }

    /**
     * Remove the specified Document from storage.
     *
     *
     * @throws Exception
     */
    public function destroy(Document $document): JsonResponse
    {
        if (! canAccessRecord(Document::class, $document->id)) {
            return $this->sendError(__('messages.flash.document_not_found'));
        }

        if (getLoggedInUser()->hasRole('Patient')) {
            if (getLoggedInUser()->owner_id != $document->patient_id) {
                return $this->sendError(__('messages.flash.document_not_found'));
            }
        }

        $this->documentRepository->deleteDocument($document->id);

        return $this->sendSuccess(__('messages.flash.document_deleted'));
    }

    /**
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     *
     * @throws FileNotFoundException
     */
    public function downloadMedia(Document $document)
    {
        if (! canAccessRecord(Document::class, $document->id)) {
            return Redirect::back();
        }

        if (getLoggedInUser()->hasRole('Patient')) {
            if (getLoggedInUser()->owner_id != $document->patient_id) {
                return Redirect::back();
            }
        }

        $documentMedia = $document->media[0];
        $documentPath = $documentMedia->getPath();
        if (config('app.media_disc') === 'public') {
            $documentPath = (Str::after($documentMedia->getUrl(), '/uploads'));
        }

        ob_end_clean();

        $file = Storage::disk(config('app.media_disc'))->get($documentPath);

        $headers = [
            'Content-Type' => $document->media[0]->mime_type,
            'Content-Description' => 'File Transfer',
            'Content-Disposition' => "attachment; filename={$document->media[0]->file_name}",
            'filename' => $document->media[0]->file_name,
        ];

        return response($file, 200, $headers);
    }
}
