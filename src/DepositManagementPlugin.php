<?php
namespace Craft;

/**
 * Deposit Management for Craft CMS
 *
 * @author    Peter van den Broek <p.vdbroek@outlook.com>
 * @copyright Copyright (c) 2017, VSR Partners
 */
class DepositManagementPlugin extends BasePlugin
{
    /**
     * @return string
     */
    public function getName(): string
    {
        return Craft::t('Deposit Management');
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return '1.0.0';
    }

    public function getSchemaVersion()
    {
        return '1.0.0';
    }

    /**
     * @return string
     */
    public function getDeveloper(): string
    {
        return 'WeAuction';
    }

    /**
     * @return string
     */
    public function getDeveloperUrl(): string
    {
        return 'http://weauction.nl';
    }

    /**
     * @return string
     */
    public function getPluginUrl()
    {
        return 'https://github.com/petervdbroek/weauction-craft-depositmanagement';
    }

    /**
     * @return string
     */
    public function getReleaseFeedUrl()
    {
        return 'https://raw.githubusercontent.com/petervdbroek/weauction-craft-depositmanagement/master/releases.json';
    }

    /**
     * Initialize plugin
     */
    public function init()
    {
        require_once __DIR__ . '/vendor/autoload.php';
    }

    /**
     * @return array
     */
    public function defineSettings(): array
    {
        return [
            'deposit_amount' => [AttributeType::String, 'default' => '250'],
            'deposit_description' => [AttributeType::String, 'default' => 'EMTA Deposit'],
        ];
    }

    /**
     * @return string
     */
    public function getSettingsHtml(): string
    {
        $filesystemConfigPath = craft()->path->getConfigPath() . 'depositmanagement.php';

        return craft()->templates->render('depositmanagement/settings', [
            'settings' => $this->getSettings(),
            'filesystemConfigExists' => (bool)IOHelper::fileExists($filesystemConfigPath)
        ]);
    }

    /**
     * @return bool
     */
    public function hasCpSection(): bool
    {
        if (!craft()->isConsole()) {
            return (craft()->userSession->isAdmin() || craft()->userSession->checkPermission('accessPlugin-depositManagement'));
        }
        return false;
    }

    /**
     * @return array
     */
    public function registerCpRoutes(): array
    {
        return [
            'depositmanagement' => ['action' => 'depositManagement/index'],
            'depositmanagement/payments' => ['action' => 'depositManagement/paymentsPage'],
        ];
    }

    /**
     * @return array
     */
    public function registerUserPermissions(): array
    {
        return [
            'managePayments' => ['label' => Craft::t('Manage payments')],
        ];
    }
}