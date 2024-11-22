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

    public static function getMonthlyStats()
    {
        return self::select(
            DB::raw('DATE_FORMAT(created_at, "%m/%Y") as month'),
            DB::raw('SUM(vacancy) as total')
        )
        ->groupBy('month')
        ->orderBy('created_at', 'desc')
        ->limit(12)
        ->get();
    }

    public static function getRecentAppointments()
    {
        return self::select(
            'subjects_rounds.*',
            'subjects.subject_group',
            'education_area.name_education'
        )
        ->join('subjects', 'subjects.id', '=', 'subjects_rounds.subject_id')
        ->join('education_area', 'education_area.id', '=', 'subjects_rounds.education_area_id')
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get();
    }

    public static function getCurrentRoundData($year, $area, $round)
    {
        return self::select(
            'subjects_rounds.*',
            'subjects.subject_group',
            'education_area.name_education',
            DB::raw('(
                SELECT SUM(sr2.vacancy)
                FROM subjects_rounds sr2
                WHERE sr2.round_year = subjects_rounds.round_year
                AND sr2.education_area_id = subjects_rounds.education_area_id
                AND sr2.subject_id = subjects_rounds.subject_id
                AND sr2.round_number <= subjects_rounds.round_number
            ) as total_appointed')
        )
        ->join('subjects', 'subjects_rounds.subject_id', '=', 'subjects.id')
        ->join('education_area', 'subjects_rounds.education_area_id', '=', 'education_area.id')
        ->where([
            'subjects_rounds.round_year' => $year,
            'subjects_rounds.education_area_id' => $area,
            'subjects_rounds.round_number' => $round,
        ])
        ->get();
    }

    public static function updateRoundItems($items)
    {
        foreach ($items as $item) {
            $appointed = $item['vacancy'];
            $remaining = $item['passed_exam'] - $item['vacancy'];

            self::where('id', $item['id'])
                ->update([
                    'subject_id' => $item['subject_id'],
                    'passed_exam' => $item['passed_exam'],
                    'appointed' => $appointed,
                    'vacancy' => $item['vacancy'],
                    'remaining' => $remaining,
                    'notes' => $item['notes'] ?? '',
                    'updated_at' => now(),
                ]);
        }
    }

    public static function updateRoundMain($oldData, $newData)
    {
        return self::where([
            'round_year' => $oldData['round_year'],
            'education_area_id' => $oldData['education_area_id'],
            'round_number' => $oldData['round_number']
        ])
        ->update([
            'round_year' => $newData['round_year'],
            'education_area_id' => $newData['education_area_id'],
            'round_number' => $newData['round_number'],
            'created_at' => $newData['created_at']
        ]);
    }

    public static function getYears()
    {
        return self::select('round_year')
            ->distinct()
            ->orderBy('round_year', 'desc')
            ->pluck('round_year');
    }

    public static function getSearchResults($request)
    {
        $query = self::select(
            'subjects_rounds.*',
            'subjects.subject_group',
            'education_area.name_education'
        )
        ->join('subjects', 'subjects.id', '=', 'subjects_rounds.subject_id')
        ->join('education_area', 'education_area.id', '=', 'subjects_rounds.education_area_id');

        if ($request->education_area) {
            $query->where('education_area_id', $request->education_area);
        }
        if ($request->subject_group) {
            $query->where('subject_id', $request->subject_group);
        }
        if ($request->year) {
            $query->where('round_year', $request->year);
        }

        return $query->orderBy('created_at', 'desc')
            ->paginate(25);
    }
}
