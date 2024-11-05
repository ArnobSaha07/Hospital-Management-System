<?php

namespace App\Repositories;

use App\Models\Accountant;
use App\Models\Income;
use App\Models\Notification;
use App\Models\User;
use Exception;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * Class IncomeRepository
 */
class IncomeRepository extends BaseRepository
{
    /**
     * @var string[]
     */
    protected $fieldSearchable = [
        'income_head',
        'name',
        'invoice_number',
        'date',
        'amount',
        'description',
    ];

    /**
     * @return array|string[]
     */
    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return Income::class;
    }

    public function store($input): bool
    {
        try {
            /* @var Income $income */
            $income = $this->create($input);

            if (! empty($input['attachment'])) {
                $income->addMedia($input['attachment'])->toMediaCollection(Income::PATH, config('app.media_disc'));
            }

            return true;
        } catch (Exception $e) {
            throw new UnprocessableEntityHttpException($e->getMessage());
        }
    }

    public function updateExpense($input, $incomeId)
    {
        try {
            /** @var Income $income */
            $input['amount'] = removeCommaFromNumbers($input['amount']);
            $income = $this->update($input, $incomeId);

            if (! empty($input['attachment'])) {
                $income->clearMediaCollection(Income::PATH);
                $income->addMedia($input['attachment'])->toMediaCollection(Income::PATH, config('app.media_disc'));
            }
            if ($input['avatar_remove'] == 1 && isset($input['avatar_remove']) && ! empty($input['avatar_remove'])) {
                removeFile($income, Income::PATH);
            }
        } catch (Exception $e) {
            throw new UnprocessableEntityHttpException($e->getMessage());
        }
    }

    public function deleteDocument($incomeId)
    {
        try {
            /** @var Income $income */
            $income = $this->find($incomeId);
            $income->clearMediaCollection(Income::PATH);
            $this->delete($incomeId);
        } catch (Exception $e) {
            throw new UnprocessableEntityHttpException($e->getMessage());
        }
    }

    public function createNotification(array $input): bool
    {
        $userIds = [];
        try {
            $accountant = Accountant::pluck('user_id', 'id')->toArray();
            foreach ($accountant as $key => $userId) {
                $userIds[$userId] = Notification::NOTIFICATION_FOR[Notification::ACCOUNTANT];
            }
            $adminUser = User::role('Admin')->first();
            $allUsers = $userIds + [$adminUser->id => Notification::NOTIFICATION_FOR[Notification::ADMIN]];
            $users = getAllNotificationUser($allUsers);

            foreach ($users as $key => $notification) {
                addNotification([
                    Notification::NOTIFICATION_TYPE['Income'],
                    $key,
                    $notification,
                    Income::INCOME_HEAD[$input['income_head']].' income added.',
                ]);
            }

            return true;
        } catch (Exception $e) {
            throw new UnprocessableEntityHttpException($e->getMessage());
        }
    }
}
