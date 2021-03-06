<?php
/**
 * Created by PhpStorm.
 * User: hahastein
 * Date: 2018/9/2
 * Time: 20:26
 */

namespace bengbeng\framework\models\platform;
use bengbeng\framework\base\BaseActiveRecord;
use bengbeng\framework\system\SettingProperty;
use yii\db\ActiveQuery;
use yii\db\Exception;

/**
 * 存储系统设置
 * Class SettingARModel
 * @property integer $setting_id 系统设置的自增ID
 * @property string $setting_name 系统设置的模快名称
 * @property string $setting_value 系统设置的对应值（字符串类型）
 * @property bool $is_system 是否为系统级的设置
 * @package bengbeng\framework\models\platform
 */
class SettingARModel extends BaseActiveRecord
{
    public static function tableName()
    {
        return '{{%setting}}';
    }

    /**
     * @return SettingProperty
     */
    public function findAllByProperty(){
        return new SettingProperty($this->dataSet(function (ActiveQuery $query){
            $query->asArray();
        }));
    }

    public function dataSave($params = []){

        foreach ($params as $key => $value){
            $this->$key = $value;
        }

        return $this->save();
    }

    /**
     * 初始化系统设置数据
     * @param array $insertValue 插入的数据
     * [
     *  [setting_model,setting_string_value,setting_int_value,is_system],
     *  [setting_model,setting_string_value,setting_int_value,is_system]
     * ]
     * @return bool
     */
    public function initData($insertValue = []){
        try{
            if(count($insertValue) <= 0){
                throw new Exception('没有初始数据');
            }

            if( \Yii::$app->db->createCommand()->batchInsert(self::tableName(), [
                'setting_model',
                'setting_string_value',
                'setting_int_value',
                'is_system'], $insertValue)->execute() ){
                return true;
            }else{
                throw new Exception('初始化系统设置失败');
            }
        }catch (Exception $ex){
            return false;
        }

    }
}