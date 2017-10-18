<?php
namespace bl\cms\shop\console\controllers;

use bl\cms\shop\console\Module;
use yii\console\Controller;

/**
 * @author Gutsulyak Vadim <guts.vadim@gmail.com>
 *
 * @property Module $module
 */
class ImportController extends Controller
{

    public function actions() {
        return $this->module->importActions;
    }

}