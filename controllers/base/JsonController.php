<?php

namespace bengbeng\framework\controllers\base;

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

    /**
     * 输出的内容
     * @var array
     */
    protected $outputData;
    /**
     * 输出的编码,默认0表示success 其它编码请查看const定义数值
     * @var int
     */
    protected $outputCode;

    protected $outputMessage;

    const CODE_SUCCESS = 10;
    const CODE_ERROR_403 = 403;
    const CODE_ERROR_404 = 404;
    const CODE_ERROR_CUSTOM = 400;

    public function init()
    {
        $this->longitude = 0;
        $this->latitude = 0;
        $this->outputCode = self::CODE_SUCCESS;

        $this->token = Yii::$app->getRequest()->getHeaders()->get('token');//获取验证token
        $this->sign = Yii::$app->getRequest()->getHeaders()->get('sign');//签名

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
        $this->longitude = Yii::$app->request->post('lng',0);
        $this->latitude = Yii::$app->request->post('lat',0);

        return parent::beforeAction($action); // TODO: Change the autogenerated stub
    }

    public function afterAction($action, $result)
    {
        if($this->outputCode == self::CODE_SUCCESS){
            return ['code' => $this->outputCode, 'data' => $this->outputData];
        }elseif ($this->outputCode == self::CODE_ERROR_CUSTOM){
            return ['code' => $this->outputCode, 'message' => $this->outputMessage, 'data' => $this->outputData];
        }else{
            return ['code' => $this->outputCode, 'message' => $this->changeCodeToString()];
        }

    }

    /**
     * @return string
     */
    private function changeCodeToString(){
        switch ($this->outputData){
            case self::CODE_ERROR_403:
                return '您无此权限访问此接口，请确定权限是否正确';
            case self::CODE_ERROR_404:
                return '没有找到任何内容';
            default:
                return '没有定义类型，请联系管理员';
        }
    }


}


