<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AutoRenginys extends Model
{
    use HasFactory;

    protected $table = 'auto_renginiai';

    protected $fillable = [
        'pavadinimas',
        'aprasymas',
        'pradzios_data',
        'pabaigos_data',
        'miestas',
        'adresas',
        'statusas',
        'organizatorius_id',
    ];

    protected $casts = [
        'pradzios_data' => 'datetime',
        'pabaigos_data' => 'datetime',
    ];

    public function organizatorius()
    {
        return $this->belongsTo(User::class, 'organizatorius_id');
    }

    public function registracijos()
{
    return $this->hasMany(\App\Models\RenginioRegistracija::class, 'auto_renginys_id');
}

}
