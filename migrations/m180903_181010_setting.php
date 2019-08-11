<?php

use yii\db\Migration;

/**
 * Class m180903_181010_yq_setting
 */
class m180903_181010_cms_comment extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180903_181010_yq_setting cannot be reverted.\n";

        return false;
    }

    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%cms_comment}}', [
            'comment_id' => $this->primaryKey(),
//            'setting_model' => $this->string(150)->notNull()->comment('设置模快名称'),
//            'setting_string_value' => $this->string(200)->defaultValue('')->comment('模块值字符串类型'),
//            'setting_int_value' => $this->integer()->defaultValue(0)->comment(' 模块值int型'),
//            'is_system' => $this->boolean()->defaultValue(1)->comment('是否是系统设置')
        ], $tableOptions);

//        $this->addColumn('{{%setting}}', 'addtime', 'INT(11) DEFAULT 0  COMMENT"添加时间" AFTER `is_system`');
    }

    public function down()
    {
        $this->dropTable('{{%cms_comment}}');
    }
}
