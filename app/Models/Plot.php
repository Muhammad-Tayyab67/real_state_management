<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plot extends Model
{
    use HasFactory;

    protected $fillable = [
        'address',
        'size',
        'price',
        'status',
        'total_share',
        'available_share',
        'sold_share',
        'description',
    ];

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function shares()
    {
        return $this->hasMany(Share::class);
    }
}
