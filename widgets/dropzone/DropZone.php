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
    public $url;
    public $options = [];
    public $eventHandlers = [];

    protected $dropzoneName = 'dropzone';

    private $htmlOptions = [];

    private $template = [
        'image' => "<div class=\"dz-image\"><img data-dz-thumbnail /></div>",
        'details' => [
            'size' => "<div class=\"dz-size\"><span data-dz-size></span></div>",
            'filename' => "<div class=\"dz-filename\"><span data-dz-name></span></div>",
//            'image' => "<img data-dz-thumbnail />"
        ],
        'progress' => "<div class=\"dz-progress\"><span class=\"dz-upload\" data-dz-uploadprogress></span></div>",
        'error-message' => "<div class=\"dz-error-message\"><span data-dz-errormessage></span></div>",
        'success-mark' => "<div class=\"dz-success-mark\"><span></span></div>",
        'error-mark' => "<div class=\"dz-error-mark\"><span></span></div>",
    ];

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
        if (empty($this->url)) {
            $this->url = Url::toRoute(['system/upload']);
        }

        $options = [
            'url' => $this->url,
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
        if(empty($this->eventHandlers['complete']) && !empty($this->url)){
            $isSingle = $this->options['maxFiles']==1?1:0;
            $this->eventHandlers['complete'] = 'function(data){$.complete.upload(data,'.$isSingle.',"'.$this->name.'")}';
        }
        if(empty($this->eventHandlers['removedfile']) && !empty($this->url)){
            $this->eventHandlers['removedfile'] = 'function(file){$.complete.removeUpload(file,"'.$this->name.'")}';
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
            $this->options['dictRemoveFile'] = '删除文件';
            $this->options['dictCancelUpload'] = '取消上传';
            $this->options['dictCancelUploadConfirmation'] = '确定取消';
        }

        if(empty($this->options['dictDefaultMessage'])){
            $dict_tag_a = Html::a('点击此处上传图片', null, ['class' => 'btn btn-info btn-lg']);
            $dict_tag_br = Html::tag('br');

            $dict_tag_div_small = Html::tag('small', sprintf('或将要上传的图片拖到这里，单次最多可选%d张', $this->options['maxFiles']));
            $dict_tag_div = Html::tag('div', $dict_tag_div_small, ['class' => 'text-center']);

            $this->options['dictDefaultMessage'] = $dict_tag_a . $dict_tag_br. $dict_tag_br. $dict_tag_div;
        }

        $this->options['previewTemplate'] = $this->getPreviewTemplate();

//        if(empty($this->options['dictRemoveFile'])){
//        }
    }

    private function getPreviewTemplate(){
        $details_size = $this->template['details']['size'];
        $details_filename = $this->template['details']['filename'];
        $details_img = empty($this->template['details']['image'])?"":$this->template['details']['image'];

        $image = "";
        if(!empty($this->template['image'])){
            $image = $this->template['image'];
        }

        $div_details = Html::tag('div', $details_size . $details_filename . $details_img, ['class' => 'dz-details']);
        $progress = $this->template['progress'];
        $error_message = $this->template['error-message'];
        $success_mark = $this->template['success-mark'];
        $error_mark = $this->template['error-mark'];

        return Html::tag('div', $image.$div_details.$progress.$error_message.$success_mark.$error_mark, [
            'class' => 'dz-preview dz-file-preview'
        ]);
    }

    private function registerAssets()
    {
        DropZoneAsset::register($this->getView());
        DropZoneTemplateAsset::register($this->getView());
        $this->getView()->registerJs('Dropzone.autoDiscover = false;',\yii\web\View::POS_END);
    }
}