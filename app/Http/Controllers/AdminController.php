<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\Admin;
use App\Models\Subject;
use App\Models\Subject_rounds;
use App\Rules\MatchOldPassword;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('admin.index');
    }
    // Education Area
    public function show_education_area()
    {
        $education_area = Admin::getEducationArea();
        return view('admin.show_education_area', compact('education_area'));
    }
    public function education_area_edit(Request $request)
    {
        $validate = $request->validate(
            [
                'id' => 'required|numeric',
            ],
            [
                'id.required' => 'กรุณาระบุรหัสสถานศึกษา',
                'id.numeric' => 'รหัสสถานศึกษาต้องเป็นตัวเลข',
            ],
        );

        $education_area = Admin::getEducationArea($request->id)->first();
        //@dd($education_area);
        if (!$education_area) {
            return redirect()->route('admin.show_education_area')->with('error', 'ไม่พบข้อมูลเขตพื้นที่การศึกษา');
        }
        return view('admin.education_area_edit', compact('education_area'));
    }
    public function education_area_update(Request $request)
    {
        $validate = $request->validate([
            'id' => 'required|numeric',
            'name' => 'required|string|max:255',
        ]);

        $education_area = Admin::where('id', $request->id)->update(['name_education' => $request->name]);

        return redirect()->route('admin.show_education_area')->with('success', 'อัพเดทข้อมูลเขตพื้นที่การศึกษาเรียบร้อยแล้ว');
    }
    public function education_area_delete(Request $request)
    {
        $validate = $request->validate([
            'id' => 'required|numeric',
        ]);

        $education_area = Admin::where('id', $request->id)->delete();

        return redirect()->route('admin.show_education_area')->with('success', 'ลบข้อมูลเขตพื้นที่การศึกษาเรียบร้อยแล้ว');
    }
    public function create_rounds()
    {
        $subjects = Subject::getSubjects();
        $education_area = Admin::getEducationArea();
        //@dd($education_area, $subjects);
        return view('admin.create_subjects', compact('subjects', 'education_area'));
    }
    public function subjects_rounds_create(Request $request)
    {
        // Validate the request
        $request->validate([
            'round_year' => 'required|integer',
            'education_area_id' => 'required|integer', //|exists:education_area,id'
            'round_number' => 'required|integer',
            'items' => 'required|array',
            'items.*.subject_id' => 'required|integer|exists:subjects,id', //|exists:subjects,id
            'items.*.passed_exam' => 'required|integer|min:0', // ผู้สอบผ่านขึ้นบัญชี
            'items.*.vacancy' => 'required|integer|min:0', // จำนวนที่บรรจุ
            'items.*.notes' => 'nullable|string',
            'created_at' => 'required|date',
        ]);

        // Process each subject item
        foreach ($request->items as $item) {
            $appointed = $item['vacancy']; // รับการบรรจุแล้ว
            $remaining = $item['passed_exam'] - $item['vacancy']; // คงเหลือ

            DB::table('subjects_rounds')->insert([
                'round_year' => $request->round_year,
                'education_area_id' => $request->education_area_id,
                'round_number' => $request->round_number,
                'subject_id' => $item['subject_id'],
                'passed_exam' => $item['passed_exam'],
                'appointed' => $appointed,
                'vacancy' => $item['vacancy'],
                'remaining' => $remaining,
                'notes' => $item['notes'] ?? '',
                'created_at' => $request->created_at,
                'updated_at' => now(),
            ]);
        }

        return redirect()->back()->with('success', 'บันทึกข้อมูลการบรรจุเรียบร้อยแล้ว');
    }
    // Change Password
    public function changePassword()
    {
        return view('auth.passwords.change');
    }
    public function changePassword_update(Request $request)
    {
        $request->validate([
            'current_password' => ['required', new MatchOldPassword()],
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        auth()
            ->user()
            ->update([
                'password' => Hash::make($request->new_password),
            ]);

        return redirect()->back()->with('success', 'Password changed successfully!');
    }

    // Profile
    public function profile_edit()
    {
        return view('admin.profile_edit');
    }
    public function profile_update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'current_password' => 'required_with:new_password',
            'new_password' => 'nullable|min:8|confirmed',
        ]);

        if ($request->filled('current_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'รหัสผ่านปัจจุบันไม่ถูกต้อง']);
            }

            $user->password = Hash::make($request->new_password);
        }

        $user->name = $request->name;
        $user->save();

        return redirect()->route('profile.edit')->with('success', 'อัพเดทโปรไฟล์เรียบร้อยแล้ว');
    }
}
