<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%short_links}}`.
 */
class m250812_004424_create_short_links_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%short_links}}', [
            'id' => $this->primaryKey(),
            'original_url' => $this->string(768)->notNull()->unique(),
            'short_code' => $this->string(10)->notNull()->unique(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        $this->batchInsert('{{%short_links}}',
            ['original_url', 'short_code', 'created_at'],
            [
                ['https://example.com/page1', 'def621', '2025-08-01 10:00:00'],
                ['https://example.com/page2', 'def622', '2025-08-02 11:00:00'],
                ['https://example.com/page3', 'def623', '2025-08-03 12:00:00'],
                ['https://example.com/page4', 'def624', '2025-08-04 13:00:00'],
                ['https://example.com/page5', 'def625', '2025-08-05 14:00:00']
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%short_links}}');
    }
}
