<?php

namespace App\Models;

use App\Models\Concerns\User\BuildsUserAttributes;
use App\Models\Concerns\User\HasUserAuthNotifications;
use App\Models\Concerns\User\HasUserNotifications;
use App\Models\Concerns\User\HasUserRelations;
use App\Models\Concerns\User\SyncsUserEmailWithPersona;
use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

/**
 * Modelo User
 *
 * @method bool hasVerifiedEmail() Determina si el usuario ha verificado su correo electrónico
 * @method bool markEmailAsVerified() Marca el correo electrónico del usuario como verificado
 * @method void sendEmailVerificationNotification() Envía la notificación de verificación de correo
 */
class User extends Authenticatable implements MustVerifyEmail
{
    use BuildsUserAttributes;
    use HasApiTokens, HasFactory, HasRoles;
    use HasUserAuthNotifications, MustVerifyEmailTrait {
        HasUserAuthNotifications::sendEmailVerificationNotification insteadof MustVerifyEmailTrait;
    }
    use HasUserNotifications, Notifiable {
        HasUserNotifications::notifications insteadof Notifiable;
        HasUserNotifications::readNotifications insteadof Notifiable;
        HasUserNotifications::unreadNotifications insteadof Notifiable;
    }
    use HasUserRelations;
    use SyncsUserEmailWithPersona;

    protected $fillable = [
        'email',
        'password',
        'status',
        'persona_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
}
