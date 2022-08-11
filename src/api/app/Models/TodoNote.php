<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TodoNote extends Model
{
    use HasFactory;
    protected $table = 'todonotes';
    protected $guarded = ['id'];
}
