<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inquilino extends Model
{

    use HasFactory, SoftDeletes;

    protected $table = 'inquilinos';

    protected $fillable = [
        "user_id",
        "nome",
        "documento",
        "email",
        "telefone",
        "observacoes",
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
