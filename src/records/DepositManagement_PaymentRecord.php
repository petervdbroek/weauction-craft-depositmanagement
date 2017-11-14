<?php
namespace Craft;

/**
 * Class DepositManagement_PaymentRecord
 * @package Craft
 *
 * @property string $payment_id
 * @property string $user_id
 * @property string $email
 * @property string $status
 */
class DepositManagement_PaymentRecord extends BaseRecord
{

    /**
     * Define table name
     *
     * @return string
     */
    public function getTableName(): string
    {
        return 'depositmanagement_payments';
    }

    /**
     * Define table attributes
     *
     * @return array
     */
    protected function defineAttributes(): array
    {
        return [
            'payment_id' => [
                AttributeType::String,
                'required'      => true
            ],
            'user_id' => [
                AttributeType::String,
                'required'      => true
            ],
            'email' => [
                AttributeType::Email,
                'required'      => true,
                'minLength'     => 5,
                'maxLength'     => 60
            ],
            'status' => [
                AttributeType::Enum,
                'required'      => true,
                'values'        => 'open,pending,cancelled,expired,failed,paid,refunded',
                'default'       => 'open'
            ],
        ];
    }

    /**
     * Define indexes
     *
     * @return array
     */
    public function defineIndexes(): array
    {
        return [
            [
                'columns' => ['payment_id'],
                'unique' => true
            ],
        ];
    }

    /**
     * @return string
     */
    public function primaryKey(): string
    {
        return 'payment_id';
    }
}