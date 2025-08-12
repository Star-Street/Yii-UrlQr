<?php

namespace app\models;

use yii\db\ActiveRecord;

class ShortLink extends ActiveRecord
{
    public static function tableName(): string
    {
        return 'short_links';
    }

    public function rules(): array
    {
        return [
            [['original_url', 'short_code'], 'required'],
            [['original_url'], 'url', 'defaultScheme' => 'http'],
            [['original_url'], 'unique'],
            [['short_code'], 'string', 'max' => 10],
            [['short_code'], 'unique'],
        ];
    }
}
