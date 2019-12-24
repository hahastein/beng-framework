<?php


namespace bengbeng\framework\components\handles;

/**
 * 优化数据体结构处理
 * Class StructureHandle
 * @package bengbeng\framework\components\handles
 */
class StructureHandle
{
    public static function NicknameAndAvatar(&$user, $key = 'user'){

        if(!isset($user[$key])){
            $user[$key]['nickname'] = $user['nickname'];
            $user[$key]['avatar_head'] = $user['avatar_head'];
        }else{
            unset($user[$key]['user_id']);
        }

    }

    /**
     * 性别相互转换
     * @param $sex
     * @return mixed
     */
    public static function Sex($sex){
        $sex_team = [
            0 => '无',
            1 => '男',
            2 => '女'
        ];

        return $sex_team[$sex];
    }

    public static function Image(&$image, $type = 'one'){

        if($type == 'one'){
            $image = $image['obj_url'];
        }else{
            foreach ($image as $key => $item){
                $image[$key] = $item['obj_url'];
            }
        }

    }
}