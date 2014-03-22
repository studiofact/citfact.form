<?php

/*
 * This file is part of the Studio Fact package.
 *
 * (c) Kulichkin Denis (onEXHovia) <onexhovia@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class citfact_form extends CModule
{
    public $MODULE_ID = 'citfact.form';
    public $MODULE_VERSION;
    public $MODULE_VERSION_DATE;
    public $MODULE_NAME;
    public $MODULE_DESCRIPTION;
    public $PARTNER_NAME;
    public $PARTNER_URI;
    public $MODULE_PATH;

    /**
     * Construct object
     */
    public function __construct()
    {
        $this->MODULE_NAME = Loc::getMessage('FORM_MODULE_NAME');
        $this->MODULE_DESCRIPTION = Loc::getMessage('FORM_MODULE_DESCRIPTION');
        $this->PARTNER_NAME = Loc::getMessage('PARTNER_NAME');
        $this->PARTNER_URI = Loc::getMessage('PARTNER_URI');
        $this->MODULE_PATH = $this->getModulePath();

        $arModuleVersion = array();
        include $this->MODULE_PATH . '/install/version.php';

        $this->MODULE_VERSION = $arModuleVersion['VERSION'];
        $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
    }

    /**
     * Return path module
     *
     * @return string
     */
    protected function getModulePath()
    {
        $modulePath = explode('/', __FILE__);
        $modulePath = array_slice($modulePath, 0, array_search($this->MODULE_ID, $modulePath) + 1);

        return join('/', $modulePath);
    }

    /**
     * Return components path for install
     *
     * @oaram bool $absolute
     * @return string
     */
    protected function getComponentsPath($absolute = true)
    {
        $documentRoot = getenv('DOCUMENT_ROOT');
        if (strpos($this->MODULE_PATH, 'local/modules') !== false) {
            $componentsPath = '/local/components';
        } else {
            $componentsPath = '/bitrix/components';
        }

        if ($absolute) {
            $componentsPath = sprintf('%s%s', $documentRoot, $componentsPath);
        }

        return $componentsPath;
    }

    /**
     * Install module
     *
     * @return void
     */
    public function DoInstall()
    {
        RegisterModule($this->MODULE_ID);

        $this->InstallFiles();
        $this->InstallDB();
        $this->InstallEvents();
    }

    /**
     * Remove module
     *
     * @return void
     */
    public function DoUninstall()
    {
        UnRegisterModule($this->MODULE_ID);

        $this->UnInstallDB();
        $this->UnInstallFiles();
        $this->UnInstallEvents();
    }

    /**
     * Add tables to the database
     *
     * @return bool
     */
    public function InstallDB()
    {
        return true;
    }

    /**
     * Remove tables from the database
     *
     * @return bool
     */
    public function UnInstallDB()
    {
        return true;
    }

    /**
     * Add post events
     *
     * @return bool
     */
    public function InstallEvents()
    {
        return true;
    }


    /**
     * Delete post events
     *
     * @return bool
     */
    public function UnInstallEvents()
    {
        return true;
    }

    /**
     * Copy files module
     *
     * @return bool
     */
    public function InstallFiles()
    {
        CopyDirFiles($this->MODULE_PATH . '/install/components', $this->getComponentsPath(), true, true);

        return true;
    }

    /**
     * Remove files module
     *
     * @return bool
     */
    public function UnInstallFiles()
    {
        DeleteDirFilesEx($this->getComponentsPath(false) . '/citfact/form');
        if (!glob($this->getComponentsPath() . '/citfact/*')) {
            @rmdir($this->getComponentsPath() . '/citfact/');
        }

        return true;
    }
}
