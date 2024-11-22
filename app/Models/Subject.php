<?php

namespace App\Models;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Subject extends Model
{
    
    protected $table = 'subjects';

    
    public static function getSubjects()
    {
        return DB::table('subjects')->get();
    }
}
