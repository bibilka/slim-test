<?php

namespace App\Models;


/**
 * Класс-модель "рефреш токен".
 * 
 * @inheritdoc
 * 
 * @property-read int $id
 * @property string $refresh_token Рефреш Токен (строковое значение)
 * @property int $access_token_id Указатель на токен доступа
 * 
 * @property-read \App\Models\AccessToken $accessToken Токен доступа, для которого был выпущен этот рефреш токен
 *
 * @property-read \Illuminate\Support\Carbon $issued_at дата выпуска рефреш-токена
 * @property-read \Illuminate\Support\Carbon $expired_at дата окончания действия рефреш-токена
 *
 */
class RefreshToken extends Model
{
    protected $table = 'oauth_refresh_tokens';

    public $timestamps = false;

    protected $casts = [
        'issued_at' => 'datetime',
        'expired_at' => 'datetime',
    ];

    public function accessToken()
    {
        return $this->belongsTo(AccessToken::class);
    }
}