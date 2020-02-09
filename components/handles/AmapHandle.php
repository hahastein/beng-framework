<?php


namespace bengbeng\framework\components\handles;


use bengbeng\framework\components\http\HttpUtil;

class AmapHandle
{

    public $key;
    public $httpUtil;

    private $result;

    public function __construct()
    {
        $this->key = isset(\Yii::$app->params['amap']['key'])?\Yii::$app->params['amap']['key']:'';
        $this->httpUtil = new HttpUtil([
            HttpUtil::MODE_KEY_HTTP => HttpUtil::HTTP_REQUEST_CURL
        ]);
    }

    public function regeo($lon, $lat){

        // location(116.310003,39.991957) 是所需要转换的坐标点经纬度，经度在前，纬度在后，经纬度间以“,”分割
        $location = $lon . "," . $lat;
        /**
         * url:https://restapi.amap.com/v3/geocode/regeo?output=xml&location=116.310003,39.991957&key=<用户的key>&radius=1000&extensions=all
         * radius（1000）为返回的附近POI的范围，单位：米
         * extensions 参数默认取值是 base，也就是返回基本地址信息
         * extensions 参数取值为 all 时会返回基本地址信息、附近 POI 内容、道路信息以及道路交叉口信息。
         * output（XML/JSON）用于指定返回数据的格式
         */
        $url = "https://restapi.amap.com/v3/geocode/regeo?output=JSON&location={$location}&key={$this->key}&radius=1000&extensions=base";
        $this->result = $this->httpUtil->request($url);

        $this->result = json_decode($this->result, true);

        return $this->result;

    }

    public function getCityInfo(){

        if(isset($this->result['regeocode']['addressComponent'])){
            $regeo = $this->result['regeocode']['addressComponent'];

            $city = $regeo['city']?$regeo['city']:$regeo['province'];
            $district = $regeo['district'];

            return $city . ' ' . $district;
        }else{
            return '';
        }
    }
}