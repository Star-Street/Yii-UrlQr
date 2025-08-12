<?php

namespace app\models;

use yii\db\ActiveRecord;

class LinkVisit extends ActiveRecord
{
    public static function tableName(): string
    {
        return 'link_visits';
    }

    public function rules(): array
    {
        return [
            [['short_link_id'], 'required'],
            [['short_link_id'], 'integer'],
            [['qty'], 'integer'],
            [['ip_source'], 'string', 'max' => 45],
        ];
    }

    public function getShortLink(): \yii\db\ActiveQuery
    {
        return $this->hasOne(ShortLink::class, ['id' => 'short_link_id']);
    }
}
