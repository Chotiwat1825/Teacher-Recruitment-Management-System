<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Admin extends Model
{
    protected $table = 'education_area';
    protected $fillable = ['name_education'];

    // ดึงข้อมูลเขตพื้นที่การศึกษา
    public static function getEducationArea($id = null)
    {
        $query = self::select('*');
        if ($id) {
            return $query->where('id', $id);
        }
        return $query->get();
    }

    // สรุปข้อมูลแดชบอร์ด
    public static function getDashboardStats()
    {
        return [
            'totalSubjects' => Subject::count(),
            'totalEducationAreas' => self::count(),
            'totalPassedExam' => Subject_rounds::sum('passed_exam'),
            'totalAppointed' => Subject_rounds::sum('vacancy')
        ];
    }

    // สถิติรายเดือน
    public static function getMonthlyStats()
    {
        return Subject_rounds::select(
            DB::raw('DATE_FORMAT(created_at, "%m/%Y") as month'),
            DB::raw('SUM(vacancy) as total')
        )
        ->groupBy('month')
        ->orderBy('created_at', 'desc')
        ->limit(12)
        ->get();
    }

    // สถิติตามกลุ่มวิชาเอก
    public static function getSubjectsStats()
    {
        return Subject_rounds::select(
            'subjects.subject_group',
            DB::raw('SUM(vacancy) as total')
        )
        ->join('subjects', 'subjects.id', '=', 'subjects_rounds.subject_id')
        ->groupBy('subjects.subject_group')
        ->orderBy('total', 'desc')
        ->limit(10)
        ->get();
    }

    // การบรรจุล่าสุด
    public static function getRecentAppointments()
    {
        return Subject_rounds::select(
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

    // ข้อมูลรอบปัจจุบัน
    public static function getCurrentRound($year, $area, $round)
    {
        return DB::table('subjects_rounds AS sr')
            ->select(
                'sr.*',
                'subjects.subject_group',
                'education_area.name_education',
                DB::raw('(
                    SELECT SUM(sr2.vacancy)
                    FROM subjects_rounds sr2
                    WHERE sr2.round_year = sr.round_year
                    AND sr2.education_area_id = sr.education_area_id
                    AND sr2.subject_id = sr.subject_id
                    AND sr2.round_number <= sr.round_number
                ) as total_appointed')
            )
            ->join('subjects', 'sr.subject_id', '=', 'subjects.id')
            ->join('education_area', 'sr.education_area_id', '=', 'education_area.id')
            ->where([
                'sr.round_year' => $year,
                'sr.education_area_id' => $area,
                'sr.round_number' => $round,
            ])
            ->get();
    }

    // อัพเดทข้อมูลการบรรจุ
    public static function updateRoundData($request)
    {
        DB::beginTransaction();
        try {
            // อัพเดทข้อมูลแต่ละรายการ
            foreach ($request->items as $item) {
                $appointed = $item['vacancy'];
                $remaining = $item['passed_exam'] - $item['vacancy'];

                DB::table('subjects_rounds')
                    ->where('id', $item['id'])
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

            // อัพเดทข้อมูลหลัก
            DB::table('subjects_rounds')
                ->where('round_year', $request->old_round_year)
                ->where('education_area_id', $request->old_education_area_id)
                ->where('round_number', $request->old_round_number)
                ->update([
                    'round_year' => $request->round_year,
                    'education_area_id' => $request->education_area_id,
                    'round_number' => $request->round_number,
                    'created_at' => $request->created_at,
                ]);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }
}
