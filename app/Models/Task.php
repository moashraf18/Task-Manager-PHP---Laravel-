<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    // These are the columns that can be mass-assigned (filled via forms)
    protected $fillable = [
        'title',
        'description',
        'status',
        'priority',
        'due_date',
    ];
}