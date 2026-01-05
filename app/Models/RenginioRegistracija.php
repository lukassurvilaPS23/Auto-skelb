<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RenginioRegistracija extends Model
{
    use HasFactory;

    protected $table = 'renginiu_registracijos';

    protected $fillable = [
        'auto_renginys_id',
        'vartotojas_id',
        'statusas',
    ];

    public function autoRenginys()
    {
        return $this->belongsTo(AutoRenginys::class, 'auto_renginys_id');
    }

    public function vartotojas()
    {
        return $this->belongsTo(User::class, 'vartotojas_id');
    }
}
