<?php
namespace Craft;

/**
 * Class DepositManagementVariable
 * @package Craft
 */
class DepositManagementVariable
{
    /**
     * @param string $status
     * @return array
     */
    public function payments(string $status): array
    {
        return craft()->depositManagement_payment->getPayments($status);
    }

    /**
     * @return array
     */
    public function statusses(): array
    {
        return ['open','pending','cancelled','expired','failed','paid','refunded'];
    }

    /**
     * @param string $email
     * @return bool
     */
    public function userDeposited(string $email): bool
    {
        return craft()->depositManagement_payment->userDeposited($email);
    }

    /**
     * @param string $status
     * @return float
     */
    public function totalAmount(string $status): float
    {
        return craft()->depositManagement_payment->totalAmount($status);
    }

    /**
     * @param string $status
     * @return int
     */
    public function numUsers(string $status = 'paid'): int
    {
        return craft()->depositManagement_payment->numUsers($status);
    }

    /**
     * @return BaseModel
     */
    public function settings(): BaseModel
    {
        return craft()->plugins->getPlugin('depositmanagement')->getSettings();
    }
}