<?php

declare(strict_types=1);

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use Bitrix\Main\ModuleManager;

Loc::loadMessages(__FILE__);

class kosmosafive_cbrrates extends CModule
{
    public $MODULE_ID = 'kosmosafive.cbrrates';
    public $MODULE_VERSION;
    public $MODULE_VERSION_DATE;
    public $MODULE_NAME;
    public $MODULE_DESCRIPTION;
    public $PARTNER_NAME;
    public $PARTNER_URI;

    public function __construct()
    {
        $arModuleVersion = [];
        include __DIR__ . '/version.php';
        if (is_array($arModuleVersion) && array_key_exists('VERSION', $arModuleVersion)) {
            $this->MODULE_VERSION = $arModuleVersion['VERSION'];
            $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        }

        $this->MODULE_NAME = Loc::getMessage('KOSMOSAFIVE_CBRRATES_MODULE_NAME');

        $missingModuleList = [];
        foreach ($this->getModulesRequired() as $moduleId) {
            if (!ModuleManager::isModuleInstalled($moduleId)) {
                $missingModuleList[] = $moduleId;
            }
        }

        $description = Loc::getMessage('KOSMOSAFIVE_CBRRATES_MODULE_DESCRIPTION');
        if (!empty($missingModuleList)) {
            $description .= '⚠️' . Loc::getMessage(
                    'KOSMOSAFIVE_CBRRATES_INSTALL_ERROR_MODULE_REQUIRED',
                    ['#MODULE_ID_LIST#' => implode(', ', $missingModuleList)]
                );
        }

        $this->MODULE_DESCRIPTION = $description;

        $this->PARTNER_NAME = Loc::getMessage('KOSMOSAFIVE_CBRRATES_PARTNER_NAME');
        $this->PARTNER_URI = Loc::getMessage('KOSMOSAFIVE_CBRRATES_PARTNER_URI');
    }

    public function GetPath($notDocumentRoot = false): string
    {
        if ($notDocumentRoot) {
            return str_ireplace(realpath(Application::getDocumentRoot()), '', dirname(__DIR__));
        }

        return dirname(__DIR__);
    }

    public function InstallDB(): void
    {
    }

    /**
     * @throws ArgumentNullException
     * @throws ArgumentException
     */
    public function UnInstallDB(): void
    {
        Option::delete($this->MODULE_ID);
    }

    public function DoInstall(): void
    {
        $this->DoInstallSilent();

        global $APPLICATION;

        $APPLICATION->IncludeAdminFile(
            Loc::getMessage('KOSMOSAFIVE_CBRRATES_INSTALL_TITLE'),
            $this->GetPath() . '/install/step.php'
        );
    }

    public function DoInstallSilent(): void
    {
        ModuleManager::registerModule($this->MODULE_ID);

        $this->InstallDB();
    }

    /**
     * @throws ArgumentNullException
     * @throws ArgumentException
     */
    public function DoUninstall(): void
    {
        global $APPLICATION;

        $context = Application::getInstance()->getContext();
        $request = $context->getRequest();
        $step = (int)$request->get('step');

        if ($step < 2) {
            $APPLICATION->IncludeAdminFile(
                Loc::getMessage('KOSMOSAFIVE_CBRRATES_UNINSTALL_TITLE'),
                $this->GetPath() . '/install/unstep1.php'
            );
        } elseif ($step === 2) {
            if ($request->get('savedata') !== 'Y') {
                $this->UnInstallDB();
            }

            ModuleManager::unRegisterModule($this->MODULE_ID);

            $APPLICATION->IncludeAdminFile(
                Loc::getMessage('KOSMOSAFIVE_CBRRATES_UNINSTALL_TITLE'),
                $this->GetPath() . '/install/unstep2.php'
            );
        }
    }

    /**
     * @throws ArgumentNullException
     * @throws ArgumentException
     */
    public function DoUninstallSilent(bool $saveData = false): void
    {
        if (!$saveData) {
            $this->UnInstallDB();
        }

        ModuleManager::unRegisterModule($this->MODULE_ID);
    }

    protected function getModulesRequired(): array
    {
        return [];
    }
}
