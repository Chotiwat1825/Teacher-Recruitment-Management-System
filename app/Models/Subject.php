<?php

namespace App\Models;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Subject extends Model
{
    protected $fillable = ['subject_group'];

    public static function getSubjects()
    {
        return self::select('subjects.*')
            ->orderBy('subject_group', 'asc')
            ->get();
    }

    public static function getSubjectsStats()
    {
        return self::select('subjects.subject_group', DB::raw('SUM(sr.vacancy) as total'))
            ->leftJoin('subjects_rounds as sr', 'subjects.id', '=', 'sr.subject_id')
            ->groupBy('subjects.subject_group')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();
    }

    public static function getSubjectsWithAppointments()
    {
        return self::select('subjects.*')
            ->join('subjects_rounds', 'subjects.id', '=', 'subjects_rounds.subject_id')
            ->distinct()
            ->get();
    }

    public static function isInUse($id)
    {
        return DB::table('subjects_rounds')
            ->where('subject_id', $id)
            ->exists();
    }
}
