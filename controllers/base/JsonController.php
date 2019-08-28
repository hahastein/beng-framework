<?php

namespace bengbeng\framework\controllers\base;

use bengbeng\framework\components\helpers\NullHelper;
use Yii;
use yii\helpers\ArrayHelper;
use yii\rest\Controller;
use yii\filters\Cors;
use yii\web\Response;
use yii\filters\ContentNegotiator;

class JsonController extends Controller{

    public $latitude;
    public $longitude;
    public $token;
    public $sign;
    public $debug;
    public $unionID;

    protected $requestParams;
    protected $nullData;

    /**
     * 输出的内容
     * @var array
     */
    protected $outputData;
    protected $outputDataExt;
    /**
     * 输出的编码,默认0表示success 其它编码请查看const定义数值
     * @var int
     */
    protected $outputCode;

    protected $outputMessage;

    const CODE_SUCCESS = 10;
    const CODE_ERROR_403 = 403;
    const CODE_ERROR_4031 = 4031;
    const CODE_ERROR_404 = 404;
    const CODE_ERROR_4001 = 4001;
    const CODE_ERROR_4002 = 4002;
    const CODE_ERROR_CUSTOM = 400;

    public function init()
    {
        $this->longitude = 0;
        $this->latitude = 0;
        $this->outputCode = self::CODE_SUCCESS;
        $this->requestParams = Yii::$app->request->post();

        $this->token = Yii::$app->getRequest()->getHeaders()->get('token');//获取验证token
        $this->sign = Yii::$app->getRequest()->getHeaders()->get('sign');//签名
        $this->debug = NullHelper::arrayKey($this->requestParams, 'debug');//调试模式
        $this->unionID = NullHelper::arrayKey($this->requestParams, 'unionid');//用户唯一标识

        unset($this->requestParams['lat'], $this->requestParams['lng']);

        //预留验证token及签名


        parent::init(); // TODO: Change the autogenerated stub
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['authenticator']);
        $allowCredentials = false;
        $requestHeader = ['*'];

        if(!empty(Yii::$app->params['origin.url']) && is_array(Yii::$app->params['origin.url']) && count(Yii::$app->params['origin.url'])>0){
            $requestHeader = Yii::$app->params['origin.url'];
            $allowCredentials = true;
        }

        $behaviors['corsFilter'] = [
            'class' => Cors::className(),
            'cors' => [
                'Origin' => $requestHeader,
                'Access-Control-Allow-Origin' => $requestHeader,
                'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'HEAD', 'OPTIONS'],
                'Access-Control-Request-Headers' => ['*'],
                'Access-Control-Allow-Credentials' => $allowCredentials,
                'Access-Control-Allow-Headers' => 'Origin, Content-Type, Accept, Referer, Authorization',
                'Access-Control-Max-Age' => 86400
            ]
        ];
        $behaviors['contentNegotiator'] = [
            'class' => ContentNegotiator::className(),
            'formats' => [
                'application/json'=> Response::FORMAT_JSON
            ]
        ];
        return $behaviors;
    }

    /**
     * @param $action
     * @return bool
     * @throws \yii\web\BadRequestHttpException
     */
    public function beforeAction($action)
    {

//        if(empty($this->token)){
//            $this->outputCode = self::CODE_ERROR_4031;
//            $this->asJson($this->splicingOutputContent())->send();
//        }

        $this->longitude = Yii::$app->request->post('lng',0);
        $this->latitude = Yii::$app->request->post('lat',0);

        return parent::beforeAction($action); // TODO: Change the autogenerated stub
    }

    public function afterAction($action, $result)
    {
        return $this->splicingOutputContent();
    }

    /**
     * 拼接输出返回内容
     * @return array
     */
    private function splicingOutputContent(){
        $output['code'] = $this->outputCode;

        if($this->outputData){
            $output['data'] = $this->outputData;
        }else{
            if(isset($this->nullData)){
                $output['data'] = $this->nullData;
            }
        }

        if($this->outputDataExt){
            foreach ($this->outputDataExt as $key => $dataExt){
                if(is_string($key)) {
                    $output[$key] = $dataExt;
                }
            }
        }

        if($this->outputCode != self::CODE_SUCCESS && $this->outputCode != self::CODE_ERROR_CUSTOM){
            $output['message'] = $this->changeCodeToString();
        }else{
            if($this->outputMessage){
                $output['message'] = $this->outputMessage;
            }
        }

        return $output;
    }

    /**
     * @return string
     */
    private function changeCodeToString(){
        switch ($this->outputCode){
            case self::CODE_ERROR_403:
                return '您无此权限访问此接口，请确定权限是否正确';
            case self::CODE_ERROR_4031:
                return 'Token失效，请重新访问接口';
            case self::CODE_ERROR_404:
                return '没有找到任何内容';
            case self::CODE_ERROR_4001:
                return '参数问题，请检查传入参数';
            case self::CODE_ERROR_4002:
                return '...';
            default:
                return '没有定义类型，请联系管理员';
        }
    }


}


