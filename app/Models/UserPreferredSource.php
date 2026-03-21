<?php

namespace App\Models;

use Database\Factories\UserPreferredSourceFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPreferredSource extends Model
{
    /** @use HasFactory<UserPreferredSourceFactory> */
    use HasFactory;

    protected $guarded = ['id'];
}
