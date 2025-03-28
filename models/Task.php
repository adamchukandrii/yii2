<?php declare(strict_types=1);

namespace app\models;

use yii\mongodb\ActiveRecord;
use function date;
use function var_dump;

class Task extends ActiveRecord
{
    public const ID = '_id';
    public const USER_ID = 'user_id';
    public const TITLE = 'title';
    public const DESCRIPTION = 'description';
    public const STATUS = 'status';
    public const START_DATE = 'start_date';

    /**
     * @return string
     */
    public static function collectionName(): string
    {
        return 'tasks';
    }

    /**
     * @return string[]
     */
    public function attributes(): array
    {
        return [self::ID, self::USER_ID, self::TITLE, self::DESCRIPTION, self::STATUS, self::START_DATE];
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [[self::USER_ID, self::TITLE, self::DESCRIPTION, self::STATUS, self::START_DATE], 'required'],
            [self::STATUS, 'in', 'range' => Status::getStatusesRange()],
            [self::START_DATE, 'default', 'value' => date('d-m-Y H:i')],
        ];
    }
}
