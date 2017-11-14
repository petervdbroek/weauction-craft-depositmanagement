<?php

namespace Craft;

/**
 * Class DepositManagementController
 * @package Craft
 */
class DepositManagementController extends BaseController
{

    /**
     * @var array
     */
    public $subNav = [];

    /**
     * Initialize plugin
     */
    public function init(): void
    {
        $this->subNav = [
            'index' => ['label' => 'Dashboard', 'url'=>'depositmanagement'],
        ];

        if (craft()->userSession->isAdmin() || craft()->userSession->checkPermission('managePayments'))
            $this->subNav['payments'] = ['label' => 'Payments', 'url' => 'depositmanagement/payments'];

        parent::init();
    }

    /**
     * Index action
     */
    public function actionIndex (): void
    {
        $this->renderTemplate('depositmanagement/index', [
            'subnav' => $this->subNav,
            'selectedSubnavItem' => 'index',
        ]);
    }

    /**
     * Payments page
     */
    public function actionPaymentsPage(): void
    {
        craft()->userSession->requirePermission('managePayments');

        $this->renderTemplate('depositmanagement/payments', array(
            'subnav' => $this->subNav,
            'selectedSubnavItem' => 'sitemap',
            'crumbs' => [
                ['label' => 'Deposit Management', 'url' => 'index'],
                ['label' => 'Payments', 'url' => 'paymentsPage'],
            ],
        ));
    }
}
