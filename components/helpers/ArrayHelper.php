<?php
/**
 * 52Beng Framework Admin
 *
 * @link http://www.52beng.com
 * @copyright Copyright © 2019 52Beng Framework. All rights reserved.
 * @author hahastein <146119@qq.com>
 * @license http://www.52beng.com/license
 * @date 2019/4/6 13:47
 */

namespace bengbeng\framework\components\helpers;


class ArrayHelper
{

    const ARRAY_FIZZY_SEARCH_MODE_KEY =  1;
    const ARRAY_FIZZY_SEARCH_MODE_VALUE =  2;

    /**
     * 按key获取数组的内容并且从数组内移除key对应的内容
     * @param string $key 键值
     * @param array $data 移除后的新数组
     * @return string|bool 返回key对应的内容
     */
    public static function returnKeyAndRemove($key, &$data){
        if(array_key_exists($key, $data)){
            $keyContent = $data[$key];
            unset($data[$key]);
            return $keyContent;
        }else{
            return false;
        }
    }

    /**
     * 模糊匹配数组内容
     * @param string|array $content 匹配的内容，如果是多个匹配，则以数组形式传入
     * @param array $data 匹配的数组
     * @param int $mode 匹配模式，ARRAY_FIZZY_SEARCH_MODE_KEY为匹配KEY ARRAY_FIZZY_SEARCH_MODE_VALUE匹配内容
     * @return array 返回匹配后的数组
     */
    public static function returnFuzzyQuery($content, $data, $mode = self::ARRAY_FIZZY_SEARCH_MODE_KEY){

        $newData = [];
        if(is_array($content)){
            $content = array_unique($content);
        }
        foreach ($data as $key => $value){

            $search = $key;
            if($mode == self::ARRAY_FIZZY_SEARCH_MODE_VALUE){
                $search = $value;
            }

            if(is_array($content)){

                foreach ($content as $item){
                    if (strstr( $search , $item ) !== false ){
                        array_push($newData, $value);
                    }
                }

            }else{
                if (strstr( $search , $content ) !== false ){
                    array_push($newData, $value);
                }
            }

        }

        return $newData;

    }
}