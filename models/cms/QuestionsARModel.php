<?php


namespace bengbeng\framework\models\cms;


use bengbeng\framework\base\BaseActiveRecord;
use bengbeng\framework\base\Enum;
use bengbeng\framework\cms\Cms;
use bengbeng\framework\models\AttachmentARModel;
use bengbeng\framework\models\CategoryARModel;
use bengbeng\framework\models\UserARModel;
use bengbeng\framework\models\UserFavoritesARModel;
use yii\db\ActiveQuery;
use yii\db\Expression;

/**
 * Class QuestionsARModel
 * @property integer $question_id
 * @property integer $url_code
 * @property string $title 标题（如果不存在标题的，从内容截取25个字做标题）
 * @property string $content 内容
 * @property integer $user_id 用户ID
 * @property integer $reply_count 回复总数
 * @property integer $view_count 浏览总数
 * @property integer $share_count 分享总数
 * @property integer $fav_count 收藏总数
 * @property bool $is_reply 是否可以回复
 * @property bool $show_img 是否给普通用户显示图片
 * @property integer $status 状态 10正常0删除4违规
 * @property integer $relation_bool 关联bool扩展字段
 * @property integer $relation_int 关联int扩展字段
 * @property integer $relation_string 关联string扩展字段
 * @property integer $createtime 创建时间
 * @property integer $updatetime 修改时间
 * @property integer $replytime 最后回复时间
 * @package bengbeng\framework\models\cms
 */
class QuestionsARModel extends BaseActiveRecord
{
    public static function tableName()
    {
        return '{{%cms_questions}}';
    }

    public function getAnswers(){
        return $this->hasMany(AnswersARModel::className(),['question_id'=>'question_id']);
    }

    public function getLastAnswer(){
        return $this->hasOne(AnswersARModel::className(),['question_id'=>'question_id'])->orderBy(['answer_id' => SORT_DESC])->limit(1);
    }

    public function getImages(){
        return $this->hasMany(AttachmentARModel::className(),['object_id'=>'question_id'])->where(['att_type' => Enum::MODULE_TYPE_FAQS])->select([
            'object_id', 'obj_url', 'is_default'
        ]);
    }

    public function getImage(){
        return $this->hasOne(AttachmentARModel::className(),['object_id'=>'question_id'])->where(['att_type' => Enum::MODULE_TYPE_FAQS])->select([
            'object_id', 'obj_url', 'is_default'
        ])->orderBy(['is_default' => SORT_DESC])->limit(1);
    }

    public function getUser(){
        return $this->hasOne(UserARModel::className(),['user_id'=>'user_id'])->select([
            'nickname',
            'avatar_head',
            'sex' => 'user_sex',
            'area' => 'gps_address',
            'user_id'
        ]);
    }

    public function getCate(){
        return $this->hasOne(CategoryARModel::className(),['cate_id'=>'cate_id'])->select([
            'cate_id',
            'cate_name'
        ]);
    }

    public function getIdentify(){
        return $this->hasMany(FaqIdentifyARModel::className(),['question_id' => 'question_id']);
    }

    public function getCelebrity(){
        return $this->hasOne(CelebrityARModel::className(),['celebrity_id' => 'celebrity_id']);
    }

    public function getFav(){
        return $this->hasOne(UserFavoritesARModel::className(),['object_id' => 'question_id']);
    }

    public function exists($question_id, $code = ''){
        $model = self::find()->where([
            'question_id' => $question_id,
        ]);
        if(!empty($code)){
            $model->andWhere([
                'url_code' => $code
            ]);
        }
        return $model->exists();
    }

    public function findAllByCateID($cate_id = 0){
        $this->with = ['identify.user'];
        if($cate_id > 0){
            return $this->findByAll(['cate_id' => $cate_id]);
        }else{
            return $this->findByAll();
        }

    }

    public function findAllByTags($tags = []){
        $this->with = ['identify.user'];

        if(count($tags) > 0){
            $tags[] = 0;
            $this->orderBy = new Expression('FIELD(cate_id, '.implode(',', $tags).') DESC, rand_show DESC');

        }
//        var_dump($this->findByAll());die;
        return $this->findByAll();

    }


    public function findAllByNoReply($cate_id = 0, $level_id = 0){
        $where = [];
        if($cate_id > 0){

            $where = [
                'cate_id' => $cate_id,
//                'reply_count' => 0
            ];

            if($level_id>0){
                $where['level_id'] = $level_id;
            }

            return $this->findByAll($where, 20);
        }else{
            if($level_id>0){
                $where['level_id'] = $level_id;
            }
            return $this->findByAll($where, 20);
        }

    }

    public function findAllByUserID($user_id){
        $this->with = ['identify.user'];
        return $this->findByMyAll([
            'user_id' => $user_id
        ]);
    }

    public function findAllByKeyword($keyword){
        $this->with = ['identify.user'];
        return $this->findByAll([
            'and',
            ['like', 'title', $keyword],
            ['or',['>', 'cate_id', 0,],['mode' => 0]]
        ]);
    }

    public function findInfoByQuestionID($id){
        return self::dataOne(function (ActiveQuery $query) use ($id){
            if($this->showField){
                $query->select($this->showField);
            }

            if($this->with && count($this->with) > 0){
                $query->with($this->with);
            }

            $query->where(['in', 'status' , [Enum::SYSTEM_STATUS_SUCCESS, 20]]);
            $query->andWhere([
                'question_id' => $id,
            ]);
        });
    }

    public function findInfoByUserAndQuestionID($user_id, $id){
        return self::dataOne(function (ActiveQuery $query) use ($user_id,$id){
            if($this->showField){
                $query->select($this->showField);
            }

            if($this->with && count($this->with) > 0){
                $query->with($this->with);
            }

            $query->where(['status' => Enum::SYSTEM_STATUS_SUCCESS]);
            $query->andWhere([
                'question_id' => $id,
                'user_id' => $user_id
            ]);
        });
    }

    public function findInfoByQuestionIDAndCode($id, $code='', $state = 10){

        if(!$this->with){
            $this->with = [];
        }
        $this->with[] = 'user';
//        $this->with[] = 'images';

        return self::dataOne(function (ActiveQuery $query) use ($id, $code, $state){
            if($this->showField){
                $query->select($this->showField);
            }

            if(is_array($state)){
                $query->where(['in' ,'status', $state]);
            }else{
                $query->where(['status' => $state]);
            }

            $query->andWhere([
                'question_id' => $id
            ]);

            if(!empty($code)){
                $query->andWhere([
                    'url_code' => $code
                ]);
            }

            $query->asArray();
        });
    }

    private function findByAll($where = false, $status = Enum::SYSTEM_STATUS_SUCCESS){
        return self::dataSet(function (ActiveQuery $query) use ($where, $status){
            if($this->showField){
                $query->select($this->showField);
            }

            if(!$this->with){
                $this->with = [];
            }

            $this->with = array_merge($this->with, ['user']);

            $query->with($this->with);

            $query->where(['status' => $status]);

            if($where){
                $query->andWhere($where);
            }

            $keyword = \Yii::$app->request->post('keyword', '');
            if(!empty($keyword)){
                $query->andWhere(['like', 'title', $keyword]);
            }

            if($this->orderBy){
                $query->orderBy($this->orderBy);
            }else{
                $query->orderBy(['updatetime' => SORT_DESC]);
            }

//            var_dump($query->createCommand()->getRawSql());die;

            $query->asArray();
        });
    }


    public function findByMyAll($where = false){
        return self::dataSet(function (ActiveQuery $query) use ($where){
            if($this->showField){
                $query->select($this->showField);
            }

            if(!$this->with){
                $this->with = [];
            }

            $this->with = array_merge($this->with, ['user']);

            $query->with($this->with);

            $query->where(['in', 'status', [10,20]]);

            if($where){
                $query->andWhere($where);
            }

            $query->orderBy(['updatetime' => SORT_DESC]);

            $query->asArray();
        });
    }

}