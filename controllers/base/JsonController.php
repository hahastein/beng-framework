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

    public function init()
    {
        $this->longitude = 0;
        $this->latitude = 0;

        $this->token = Yii::$app->getRequest()->getHeaders()->get('token');//获取验证token
        $this->sign = Yii::$app->getRequest()->getHeaders()->get('sign');//签名


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
        if(empty($result[0])){
            return $result;
        }

        $returnContent = [
            'code' => 400,
            'data' => false
        ];
        $array_record = 0;
        foreach ($result as $item){
            if(is_object($item)){
                if($array_record == 0) {
                    $returnContent['data'] = ArrayHelper::toArray($item);
                }else{
                    $returnContent['data'.$array_record] = ArrayHelper::toArray($item);
                }
                $array_record ++;
            } else if(is_array($item)){
                if($array_record == 0){
                    $returnContent['data'] = $item;
                }else{
                    $returnContent['data'.$array_record] = $item;
                }
                $array_record ++;
            }else if(is_string($item)){
                if(isset($returnContent['msg'])){
                    if($array_record == 0){
                        $returnContent['data'] = $item;
                    }else{
                        $returnContent['data'.$array_record] = $item;
                    }
                    $array_record ++;
                }else {
                    $returnContent['msg'] = $item;
                }
            }else{
                $returnContent['code'] = $item;
            }
        }

        if(!isset($returnContent['msg'])){
            $returnContent['msg'] = "接口数据出现错误";
        }

        return $returnContent;
    }


}


