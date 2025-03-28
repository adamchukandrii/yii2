<?php declare(strict_types=1);

namespace app\models;

use yii\mongodb\ActiveRecord;
use function date;

class User extends ActiveRecord
{
    public const ID = '_id';
    public const LOGIN = 'login';
    public const PASSWORD = 'password';
    public const FIRST_NAME = 'first_name';
    public const LAST_NAME = 'last_name';
    public const EMAIL = 'email';
    public const REGISTRATION_DATE = 'registration_date';

    /**
     * @return string
     */
    public static function collectionName(): string
    {
        return 'users';
    }

    /**
     * @return string[]
     */
    public function attributes(): array
    {
        return [
            self::ID,
            self::LOGIN,
            self::PASSWORD,
            self::FIRST_NAME,
            self::LAST_NAME,
            self::EMAIL,
            self::REGISTRATION_DATE
        ];
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [[self::LOGIN, self::PASSWORD, self::FIRST_NAME, self::LAST_NAME, self::EMAIL], 'required'],
            [[self::LOGIN, self::EMAIL], 'unique'],
            [self::LOGIN, 'string', 'min' => 4],
            [self::PASSWORD, 'match', 'pattern' => '/^[a-zA-Z\d_\-,.]{6,}$/'],
            [self::FIRST_NAME, 'match', 'pattern' => '/^[A-Z][a-z]+$/'],
            [self::LAST_NAME, 'match', 'pattern' => '/^[A-Z][a-z]+$/'],
            [self::EMAIL, self::EMAIL],
            [self::REGISTRATION_DATE, 'default', 'value' => date('d-m-Y H:i')],
        ];
    }
}
