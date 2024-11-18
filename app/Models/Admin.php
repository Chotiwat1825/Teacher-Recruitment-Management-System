<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Admin extends Model
{
    protected $table = 'education_area';
    protected $fillable = ['name_education'];

    public static function getEducationArea()
    {
        return DB::table('education_area')->get();
    }
}
