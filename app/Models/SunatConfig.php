<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class SunatConfig extends Model
{
    protected $fillable = [
        'ruc',
        'razon_social',
        'sol_user',
        'sol_password',
        'fe_wsdl',
        'gre_wsdl',
        'cert_path',
        'cert_password',
        'is_enabled',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
    ];

    public function setSolPasswordAttribute($value): void
    {
        $this->attributes['sol_password'] = $value !== null && $value !== '' ? Crypt::encryptString($value) : null;
    }

    public function getSolPasswordAttribute($value): ?string
    {
        if (!$value) return null;
        try {
            return Crypt::decryptString($value);
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function setCertPasswordAttribute($value): void
    {
        $this->attributes['cert_password'] = $value !== null && $value !== '' ? Crypt::encryptString($value) : null;
    }

    public function getCertPasswordAttribute($value): ?string
    {
        if (!$value) return null;
        try {
            return Crypt::decryptString($value);
        } catch (\Throwable $e) {
            return null;
        }
    }
}
