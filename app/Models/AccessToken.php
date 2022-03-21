<?php

namespace App\Models;

use App\Models\Traits\Tokenable;

/**
 * Класс-модель "токен доступа".
 * 
 * @inheritdoc
 * 
 * @property-read int $id
 * @property int $user_id Указатель на пользователя
 * @property bool $revoked является ли токен использованным (аннулированным)
 * @property string $identifier Идентификатор
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
    use Tokenable;
    
    /**
     * @var string
     */
    protected $table = 'oauth_access_tokens';

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var array
     */
    protected $casts = [
        'issued_at' => 'datetime',
        'expired_at' => 'datetime',
        'revoke' => 'boolean'
    ];

    /**
     * Рефреш токены для данного токена доступа.
     */
    public function refreshTokens()
    {
        return $this->hasMany(RefreshToken::class);
    }

    /**
     * Пользователь, который обладает данным токеном доступа.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}