<?php

use yii\db\Migration;

/**
 * Create static URLs table
 */
class m241229_120000_create_static_urls_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%static_urls}}', [
            'id' => $this->primaryKey(),
            'url' => $this->string(255)->notNull()->unique(),
            'controller' => $this->string(100)->notNull(),
            'action' => $this->string(100)->notNull(),
            'params' => $this->json(),
            'status' => $this->smallInteger()->defaultValue(10),
            'created_at' => $this->timestamp()->notNull()->defaultExpression(new \yii\db\Expression('NOW()')),
            'updated_at' => $this->timestamp()->notNull()->defaultExpression(new \yii\db\Expression('NOW()')),
        ]);
        $this->addCommentOnTable('{{%static_urls}}', 'Static URLs');
        $this->createIndex('idx-static_urls-url', '{{%static_urls}}', 'url');
        $this->createIndex('idx-static_urls-status', '{{%static_urls}}', 'status');
        $this->insert('{{%static_urls}}', [
            'url' => 'about-us',
            'controller' => 'site',
            'action' => 'about',
            'params' => [],
            'status' => 10,
        ]);
        $this->insert('{{%static_urls}}', [
            'url' => 'contact',
            'controller' => 'site',
            'action' => 'contact',
            'params' => [],
            'status' => 10,
        ]);
        $this->insert('{{%static_urls}}', [
            'url' => 'terms',
            'controller' => 'site',
            'action' => 'terms',
            'params' => [],
            'status' => 10,
        ]);
    }
    public function safeDown()
    {
        $this->dropTable('{{%static_urls}}');
    }
} 