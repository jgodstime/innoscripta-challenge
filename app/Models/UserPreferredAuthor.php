<?php

namespace App\Models;

use Database\Factories\UserPreferredAuthorFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPreferredAuthor extends Model
{
    /** @use HasFactory<UserPreferredAuthorFactory> */
    use HasFactory;

    protected $guarded = ['id'];
}
