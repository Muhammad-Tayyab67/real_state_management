<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Share extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'plot_id',
        'share_number',
        'share_price',
        'share_status'
    ];
}
