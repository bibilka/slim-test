<?php

namespace App\Models;

use App\Models\Traits\Tokenable;

/**
 * Класс-модель "рефреш токен".
 * 
 * @inheritdoc
 * 
 * @property-read int $id
 * @property int $access_token_id Указатель на токен доступа
 * @property bool $revoked является ли токен использованным (аннулированным)
 * @property string $identifier Идентификатор
 * 
 * @property-read \App\Models\AccessToken $accessToken Токен доступа, для которого был выпущен этот рефреш токен
 *
 * @property-read \Illuminate\Support\Carbon $issued_at дата выпуска рефреш-токена
 * @property-read \Illuminate\Support\Carbon $expired_at дата окончания действия рефреш-токена
 *
 */
class RefreshToken extends Model
{
    use Tokenable;

    /**
     * @var string
     */
    protected $table = 'oauth_refresh_tokens';

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
     * Токен доступа, к которому относится данный Refresh токен.
     */
    public function accessToken()
    {
        return $this->belongsTo(AccessToken::class);
    }
}