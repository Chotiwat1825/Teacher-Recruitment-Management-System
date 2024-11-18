<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\Admin;
use App\Models\Subject;

class Subject_rounds extends Model
{
    protected $table = 'subjects_rounds';

    protected $fillable = ['round_year', 'education_area_id', 'round_number', 'subject_id', 'passed_exam', 'appointed', 'vacancy', 'remaining', 'notes'];

    public static function getSubjectRounds()
    {
        return DB::table('subjects_rounds')->get();
    }
    // Relationship with EducationArea (assuming you have this model)
    public function educationArea()
    {
        return $this->belongsTo(Admin::class);
    }

    // Relationship with Subject (assuming you have this model)
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}
