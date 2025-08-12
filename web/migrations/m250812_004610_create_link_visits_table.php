<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%link_visits}}`.
 */
class m250812_004610_create_link_visits_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%link_visits}}', [
            'id' => $this->primaryKey(),
            'short_link_id' => $this->integer()->notNull(),
            'ip_source' => $this->string(45),
            'qty' => $this->integer()->notNull(),
            'visited_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        $this->addForeignKey(
            'fk_link_visits_short_link_id',
            'link_visits',
            'short_link_id',
            'short_links',
            'id',
            'CASCADE'
        );

        $this->batchInsert('{{%link_visits}}',
            ['short_link_id', 'ip_source', 'qty', 'visited_at'],
            [
                [1, '192.168.1.1', 1, '2025-08-01 10:30:00'],
                [2, '192.168.1.2', 2, '2025-08-02 11:30:00'],
                [3, '192.168.1.3', 3, '2025-08-03 12:30:00'],
                [4, '192.168.1.4', 4, '2025-08-04 13:30:00'],
                [5, '192.168.1.5', 5, '2025-08-05 14:30:00']
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_link_visits_short_link_id', '{{%link_visits}}');
        $this->dropTable('{{%link_visits}}');
    }
}
