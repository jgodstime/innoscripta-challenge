<?php

namespace App\Models;

use Database\Factories\UserPreferredCategoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPreferredCategory extends Model
{
    /** @use HasFactory<UserPreferredCategoryFactory> */
    use HasFactory;

    protected $guarded = ['id'];
}
