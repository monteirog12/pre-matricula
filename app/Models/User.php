<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**Nova function para mermitir apenas admin de acessar register */
    /**public function escola()
    {
        return $this->belongsTo(\App\Models\Escola::class);
    }*/
    
    /* Permitir um usuario do tipo operador acessar uma ou mais escolas definidas */
    public function escolas()
    {
        return $this->belongsToMany(\App\Models\Escola::class, 'escola_usuario');
    }

    // Admin acessa tudo; Operador só as escolas vinculadas a ele.
    public function podeAcessarEscola(int $escolaId): bool
    {
        return $this->perfil === 'Admin' || $this->escolas->contains('id', $escolaId);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
