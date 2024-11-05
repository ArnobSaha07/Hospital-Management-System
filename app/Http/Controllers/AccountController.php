<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAccountRequest;
use App\Http\Requests\UpdateAccountRequest;
use App\Models\Account;
use App\Models\Payment;
use App\Repositories\AccountRepository;
use Exception;
use Flash;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\View\View;

class AccountController extends AppBaseController
{
    /** @var AccountRepository */
    private $accountRepository;

    public function __construct(AccountRepository $accountRepo)
    {
        $this->accountRepository = $accountRepo;
    }

    /**
     * Display a listing of the Account.
     *
     * @return Factory|View
     *
     * @throws Exception
     */
    public function index(Request $request): View
    {
        $input = $request->all();
        $data['statusArr'] = Account::STATUS_ARR;
        $data['typeArr'] = Account::TYPE_ARR;

        return view('accounts.index')->with($data);
    }

    /**
     * Store a newly created Account in storage.
     */
    public function store(CreateAccountRequest $request): JsonResponse
    {
        $input = $request->all();

        $this->accountRepository->create($input);

        return $this->sendSuccess(__('messages.flash.account_save'));
    }

    /**
     * @param  int  $i
     * @return Application|Factory|RedirectResponse|Redirector|View
     */
    public function show($id)
    {
        $account = Account::find($id);
        if (empty($account)) {
            Flash::error('Account not found');

            return redirect(route('accounts.index'));
        }
        $payments = $account->payments;

        return view('accounts.show', compact('payments', 'account'));
    }

    /**
     * Show the form for editing the specified Account.
     */
    public function edit(Account $account): JsonResponse
    {
        if (! canAccessRecord(Account::class, $account->id)) {
            return $this->sendError(__('messages.flash.not_allow_access_record'));
        }

        return $this->sendResponse($account, __('messages.flash.account_retrieved'));
    }

    /**
     * Update the specified Account in storage.
     */
    public function update(Account $account, UpdateAccountRequest $request): JsonResponse
    {
        $this->accountRepository->update($request->all(), $account->id);

        return $this->sendSuccess(__('messages.flash.account_update'));
    }

    /**
     * Remove the specified Account from storage.
     *
     *
     * @throws Exception
     */
    public function destroy(Account $account): JsonResponse
    {
        if (! canAccessRecord(Account::class, $account->id)) {
            return $this->sendError(__('messages.flash.accountant_not_found'));
        }
        $accountModel = [
            Payment::class,
        ];
        $result = canDelete($accountModel, 'account_id', $account->id);
        if ($result) {
            return $this->sendError(__('messages.flash.account_cant_delete'));
        }
        $this->accountRepository->delete($account->id);

        return $this->sendSuccess(__('messages.flash.account_delete'));
    }

    public function activeDeactiveAccount(Account $account): JsonResponse
    {
        if (! canAccessRecord(Account::class, $account->id)) {
            return $this->sendError(__('messages.flash.accountant_not_found'));
        }
        $account->status = ! $account->status;
        $account->save();

        return $this->sendSuccess(__('messages.flash.account_update'));
    }
}
