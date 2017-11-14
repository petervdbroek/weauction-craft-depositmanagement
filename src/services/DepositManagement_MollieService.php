<?php
namespace Craft;

use Mollie_API_Client;
use Mollie_API_Exception;

/**
 * Class DepositManagement_MollieService
 * @package Craft
 */
class DepositManagement_MollieService extends BaseApplicationComponent
{
    /**
     * @var Mollie_API_Client;
     */
    private $mollie;

    /** $var string */
    private $mollieError = '';

    /**
     * DepositManagement_MollieService constructor
     */
    public function __construct()
    {
        $mollie_api_key = $access_token = craft()->config->get('mollie_api_key', 'depositmanagement');
        $this->mollie = new Mollie_API_Client;
        $this->mollie->setApiKey($mollie_api_key);
    }

    /**
     * @param string $paymentId
     * @return bool
     */
    public function updatePayment(string $paymentId): bool
    {
        try {
            $payment = $this->mollie->payments->get($paymentId);
        } catch (Mollie_API_Exception $e) {

            $this->mollieError = $e->getMessage();
            return false;
        }

        /** @var DepositManagement_PaymentRecord $paymentRecord */
        $paymentRecord = DepositManagement_PaymentRecord::model()->findById($payment->id);
        $paymentRecord->setAttribute('status', $payment->status);
        $paymentRecord->save();

        if ($payment->status == 'paid') { // Send mail
            $settings = craft()->plugins->getPlugin('depositmanagement')->getSettings();
            $user = craft()->myAuction_login->getUserById($paymentRecord->user_id);
            if ($user->email[0]->value) {
                $recipients = $user->email[0]->value;

                $replaceVars = [
                    'deposit' => '&euro; ' . number_format($settings['deposit_amount'], 0, ',', '.'),
                    'name'   => $user->profile->firstname
                ];

                $locale = ($user->profile->language) ? $user->profile->language : 'en';
                /** @var EntryModel $mail */
                $mail = craft()->myAuction_craft->getMail($locale, 'confirmationOfDeposit');
                $body = $mail->getContent()->getAttribute('mailBody');
                $subject = $mail->getContent()->getAttribute('subject');

                foreach ($replaceVars AS $replaceVar => $value) {
                    $body = str_replace('%' . $replaceVar . '%', $value, $body);
                    $subject = str_replace('%' . $replaceVar . '%', $value, $subject);
                }

                $email = new EmailModel();
                $email->toEmail = $recipients;
                $email->subject = $subject;
                $email->body    = $body;

                if (craft()->email->sendEmail($email)) {
                    return true;
                }
            }
            return false;
        }

        return true;
    }

    /**
     * @param string $user_id
     * @param string $email
     * @return null|string
     */
    public function createPayment(string $user_id, string $email): ?string
    {
        $settings = craft()->plugins->getPlugin('depositmanagement')->getSettings();
        try {
            $payment = $this->mollie->payments->create(
                [
                    'amount'      => $settings['deposit_amount'],
                    'description' => $settings['deposit_description'],
                    'redirectUrl' => craft()->myAuction_craft->getUrl('myAccount') . '?activeTab=deposit',
                    'webhookUrl'  => craft()->config->get('environmentVariables')['baseUrl'] . 'actions/depositManagement/payment/paymentWebhook',
                    'metadata'    => [
                        'email' => $email
                    ]
                ]
            );

            $paymentRecord = new DepositManagement_PaymentRecord();
            $paymentRecord->setAttribute('payment_id', $payment->id);
            $paymentRecord->setAttribute('user_id', $user_id);
            $paymentRecord->setAttribute('email', $email);
            $paymentRecord->save();

            return $payment->getPaymentUrl();
        } catch (Mollie_API_Exception $e) {
            $this->mollieError = $e->getMessage();

            return false;
        }
    }

    /**
     * @param string $paymentId
     * @return bool
     */
    public function refundPayment(string $paymentId): bool
    {
        try {
            $payment = $this->mollie->payments->get($paymentId);
            $this->mollie->payments->refund($payment);

            $paymentRecord = DepositManagement_PaymentRecord::model()->findById($payment->id);
            $paymentRecord->setAttribute('status', 'pending');
            $paymentRecord->save();

            return true;

        } catch (Mollie_API_Exception $e) {
            $this->mollieError = $e->getMessage();

            return false;
        }
    }

    /**
     * @return string
     */
    public function getMollieError(): string
    {
        return $this->mollieError;
    }
}
