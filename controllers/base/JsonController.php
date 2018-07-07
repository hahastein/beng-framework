<?php

namespace bengbeng\framework\controllers\base;

use Yii;
use yii\helpers\ArrayHelper;
use yii\rest\Controller;
use yii\filters\Cors;
use yii\web\Response;
use yii\filters\ContentNegotiator;

class JsonController extends Controller{

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['authenticator']);
        $behaviors['corsFilter'] = [
            'class' => Cors::className(),
            'cors' => [
                'Origin' => ['*'],
                'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'HEAD', 'OPTIONS'],
                'Access-Control-Request-Headers' => ['*'],
                'Access-Control-Allow-Credentials' => false,
                'Access-Control-Max-Age' => 86400,
                'Access-Control-Expose-Headers' => []
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


