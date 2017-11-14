<?php
namespace Craft;

/**
 * Class DepositManagement_PaymentController
 * @package Craft
 */
class DepositManagement_PaymentController extends BaseController
{
    /**
     * @var array
     */
    protected $allowAnonymous = ['actionCreatePayment', 'actionPaymentWebhook'];

    /**
     * Refund amount to user
     */
    public function actionRefund(): void
    {
        $params = $this->getActionParams();

        if (craft()->depositManagement_mollie->refundPayment($params['id'])) {
            craft()->userSession->setNotice(Craft::t('Payment refunded'));
        } else {
            craft()->userSession->setError(craft()->depositManagement_mollie->getMollieError());
        }

        craft()->request->redirect('/admin/depositmanagement/payments');
    }

    /**
     * Create payment
     */
    public function actionCreatePayment(): void
    {
        $user = craft()->myAuction_login->getUser();
        $redirectUrl = craft()->depositManagement_mollie->createPayment($user->uuid, $user->email[0]->value);
        craft()->request->redirect($redirectUrl);
    }

    /**
     * Payment webhook from Mollie
     */
    public function actionPaymentWebhook(): void
    {
        $this->requirePostRequest();

        if (craft()->depositManagement_mollie->updatePayment($_POST['id'])) {
            http_response_code(200);
        } else {
            echo craft()->depositManagement_mollie->getMollieError();
            http_response_code(500);
        }
        die();
    }
}
