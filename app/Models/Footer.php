<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Footer extends Model
{
    protected $fillable = [
        'company',
        'address_1',
        'address_2',
        'address_3',
        'phone_1',
        'phone_2',
        'fax',
        'email',
        'map_url',
        'quick_links',
        'follow_us_desc',
        'social_links'
    ];
    protected $casts = [
        'quick_links' => 'array',
        'social_links' => 'array'
    ];
}