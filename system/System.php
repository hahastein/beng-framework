<?php


namespace bengbeng\framework\system;

use bengbeng\framework\base\Bootstrap;

/**
 * Class System
 * @property SettingLogic $setting
 * @property AttachmentLogic $attachment
 * @package bengbeng\framework\system
 */
class System extends Bootstrap
{
    public function init()
    {
        parent::init();
        $this->moduleName = 'system';
    }
}