<?php
namespace Craft;

/**
 * Class DepositManagement_PaymentService
 * @package Craft
 */
class DepositManagement_PaymentService extends BaseApplicationComponent
{
    /**
     * @param string $status
     * @return array
     */
    public function getPayments(string $status): array
    {
        if ($status == 'all') {
            return DepositManagement_PaymentRecord::model()->findAll();
        } else {
            return DepositManagement_PaymentRecord::model()->findAll(
                'status=:status',
                [':status' => $status]
            );
        }
    }

    /**
     * @param string $email
     * @return bool
     */
    public function userDeposited(string $email): bool
    {
        /** @var DepositManagement_PaymentRecord $payment */
        $payment = DepositManagement_PaymentRecord::model()->findAll(
            "email=:email AND status = 'paid'",
            [':email' => $email]
        );

        if ($payment) {
            return true;
        }
        return false;
    }

    /**
     * @param string $status
     * @return float
     */
    public function totalAmount(string $status): float
    {
        $settings = craft()->plugins->getPlugin('depositmanagement')->getSettings();

        $res = DepositManagement_PaymentRecord::model()->countByAttributes([
            'status' => $status
        ]);

        return $res * $settings['deposit_amount'];
    }

    /**
     * @param string $status
     * @return int
     */
    public function numUsers(string $status): int
    {
        $res = DepositManagement_PaymentRecord::model()->countByAttributes([
            'status' => $status
        ]);

        return $res;
    }
}