<?php
/**
 * Created by PhpStorm.
 * User: hahastein
 * Date: 2018/9/6
 * Time: 1:54
 */

namespace bengbeng\framework\widgets\dropzone;

use Yii;
use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;

class DropZone extends Widget
{
    public $name = '';
    public $uploadUrl;
    public $options = [];
    public $eventHandlers = [];

    protected $dropzoneName = 'dropzone';

    private $htmlOptions = [];

    public function init()
    {
        parent::init(); // TODO: Change the autogenerated stub

        $this->dropzoneName = 'dropzone_' . $this->id;

        Html::addCssClass($this->htmlOptions, 'dropzone');
//        Html::addCssClass($this->messageOptions, 'dz-message');

        if(empty($this->name)){
            $this->name = $this->dropzoneName;
        }

        $this->loadDefaultOption();
        $this->loadEventHandlers();

    }

    public function run()
    {
        if (empty($this->uploadUrl)) {
            $this->uploadUrl = Url::toRoute(['system/upload']);
        }

        $options = [
            'uploadUrl' => $this->uploadUrl,
            'paramName' => $this->name,
            'params' => [],
        ];

        if (Yii::$app->request->enableCsrfValidation) {
            $options['params'][Yii::$app->request->csrfParam] = Yii::$app->request->getCsrfToken();
        }

        $this->htmlOptions['id'] = $this->id;
        $this->options = ArrayHelper::merge($this->options, $options);
        echo Html::tag('div', '', $this->htmlOptions);

        $this->registerAssets();
        $this->createDropzone();

        foreach ($this->eventHandlers as $event => $handler) {
            $handler = new \yii\web\JsExpression($handler);
            $this->getView()->registerJs(
                $this->dropzoneName . ".on('{$event}', {$handler})",\yii\web\View::POS_END
            );
        }


    }

    protected function createDropzone()
    {
        $options = Json::encode($this->options);
        $this->getView()->registerJs($this->dropzoneName . ' = new Dropzone("#' . $this->id . '", ' . $options . ');',\yii\web\View::POS_END);
    }


    /**
     * 私有方法
     */

    private function loadEventHandlers(){
        if(empty($this->eventHandlers['complete']) && !empty($this->uploadUrl)){
            $this->eventHandlers['complete'] = 'function(data){$.complete.upload(data)}';
        }
    }

    private function loadDefaultOption(){
        if(empty($this->options['maxFiles'])){
            $this->options['maxFiles'] = '5';
        }

        if(empty($this->options['acceptedFiles'])){
            $this->options['acceptedFiles'] = '.jpg,.jpeg,.gif,.png';
        }

        if(empty($this->options['addRemoveLinks'])){
            $this->options['addRemoveLinks'] = 'true';
        }

        if(empty($this->options['dictDefaultMessage'])){
            $dict_tag_a = Html::a('点击此处上传图片', null, ['class' => 'btn btn-info btn-lg']);
            $dict_tag_br = Html::tag('br');

            $dict_tag_div_small = Html::tag('small', sprintf('或将要上传的图片拖到这里，单次最多可选%d张', $this->options['maxFiles']));
            $dict_tag_div = Html::tag('div', $dict_tag_div_small, ['class' => 'text-center']);

            $this->options['dictDefaultMessage'] = $dict_tag_a . $dict_tag_br. $dict_tag_br. $dict_tag_div;
        }

        if(empty($this->options['dictRemoveFile'])){
            $this->options['dictRemoveFile'] = '删除文件';
        }
    }

    private function registerAssets()
    {
        DropZoneAsset::register($this->getView());
        $this->getView()->registerJs('Dropzone.autoDiscover = false;',\yii\web\View::POS_END);
    }
}