<?php
/**
 * Created by PhpStorm.
 * User: hahastein
 * Date: 2018/9/15
 * Time: 23:29
 */

namespace bengbeng\framework\widgets\editor\summernote;

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\InputWidget;

class Summernote extends InputWidget
{
    public $clientOptions = [];
    public $options = [];

    //默认配置项
    private $defaultOptions = ['class' => 'form-control'];
    private $defaultClientOptions = [
        'height' => 200,
        'codemirror' => [
            'theme' => 'monokai',
        ],
    ];

    public function init()
    {
        $this->options = array_merge($this->defaultOptions, $this->options);
        $this->clientOptions = array_merge($this->defaultClientOptions, $this->clientOptions);

        parent::init(); // TODO: Change the autogenerated stub
    }

    public function run()
    {
        $this->registerAssets();

        echo $this->hasModel()
            ? Html::activeTextarea($this->model, $this->attribute, $this->options)
            : Html::textarea($this->name, $this->value, $this->options);

        $callbacks = $this->getExtendsParams('callbacks');
        $buttons = $this->getExtendsParams('buttons');
        $modules = $this->getExtendsParams('modules');

        $clientOptions = empty($this->clientOptions)
            ? null
            : Json::encode($this->clientOptions);

        $this->getView()->registerJs('
            var params = ' . $clientOptions . ';'
            . (empty($callbacks) ? '' : 'params.callbacks = { ' . $callbacks . ' };')
            . (empty($buttons) ? '' : 'params.buttons = { ' . $buttons . ' };')
            . (empty($modules) ? '' : 'params.modules = { ' . $modules . ' };')
            . 'jQuery( "#' . $this->options['id'] . '" ).summernote(params);
        ');
    }

    private function registerAssets(){

        $view = $this->getView();

        if (ArrayHelper::getValue($this->clientOptions, 'codemirror')) {
            CodemirrorAsset::register($view);
        }

        SummernoteAsset::register($view);

        if ($language = ArrayHelper::getValue($this->clientOptions, 'lang', null)) {
            SummernoteLanguageAsset::register($view)->language = $language;
        }
    }

    private function getExtendsParams($param)
    {
        $result = '';
        foreach (ArrayHelper::remove($this->clientOptions, $param, []) as $val => $key) {
            $result .= (empty($result) ? '' : ',') . $val . ': ' . $key;
        }

        return $result;
    }
}