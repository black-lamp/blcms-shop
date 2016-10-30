<?php
namespace bl\cms\shop\widgets;

use yii\base\Widget;

/**
 * This widget adds language switcher button.
 *
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */
class LanguageSwitcher extends Widget
{
    public $languages;
    public $selectedLanguage;
    public $model;

    /**
     * @inheritdoc
     */
    public function run()
    {
        return $this->render('language-switcher', [
            'languages' => $this->languages,
            'selectedLanguage' => $this->selectedLanguage,
            'model' => $this->model
        ]);
    }
}