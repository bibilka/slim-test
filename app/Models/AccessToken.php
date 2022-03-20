<?php

namespace App\Models;

/**
 * Класс-модель "токен доступа".
 * 
 * @inheritdoc
 * 
 * @property-read int $id
 * @property string $token Токен (строковое значение)
 * @property int $user_id Указатель на пользователя
 * 
 * @property-read \App\Models\User $user Пользователь кому принадлежит токен доступа
 *
 * @property-read \Illuminate\Support\Carbon $issued_at дата выпуска токена
 * @property-read \Illuminate\Support\Carbon $expired_at дата окончания действия токена
 * 
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\RefreshToken[] $refreshTokens рефреш токены
 *
 */
class AccessToken extends Model
{
    protected $table = 'oauth_access_tokens';

    public $timestamps = false;

    protected $casts = [
        'issued_at' => 'datetime',
        'expired_at' => 'datetime',
    ];

    public function refreshTokens()
    {
        return $this->hasMany(RefreshToken::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}