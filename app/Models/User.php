<?php

namespace App\Models;

/**
 * Класс-модель "пользователь".
 * 
 * @inheritdoc
 * 
 * @property-read int $id
 *
 * @property string $name Имя пользователя
 * @property string $password Пароль
 *
 * @property-read \Illuminate\Support\Carbon $created_at Дата регистрации
 * @property-read \Illuminate\Support\Carbon $updated_at Дата обновления
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\AccessToken[] $tokens Токены доступа
 *
 */
class User extends Model
{
    /**
     * @var string
     */
    protected $table = 'users';

    /**
     * Токены доступа пользователя.
     */
    public function tokens()
    {
        return $this->hasMany(AccessToken::class);
    }
}