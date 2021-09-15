<?php
  
namespace App\Http\Controllers;
  
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\inviteEmail;

use Validator;
use Hash;
// use Session;
use App\Models\User;
use App\Models\School;
use App\Models\Admin;
use App\Models\Teacher;
use App\Models\Student;

  
  
class UniversalController extends Controller
{
    public function inviteTeacher(Request $request)
    {
        $rules = [
            'email'     => 'required|email|min:8|max:50|unique:users,email',
        ];

        $validator = Validator::make($request->all(), $rules);
  
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        if ($request->user()->role!='adm') {
            return response(403);
        }
        
        $school = Admin::select('school')->where('user',$request->user()->id)->first();
        $school = json_decode($school)->school;
        $school = School::find($school);

        $password = substr(md5(microtime()),rand(0,5),12);
        $new_user = new User;
        $new_user->email = strtolower($request->email);
        $new_user->password = Hash::make($password);
        $new_user->email_verified_at = \Carbon\Carbon::now();
        $new_user->role = 'tch';
        $new_user->save();
        
        $teacher = new Teacher;
        $teacher->code = 'T'.strtotime(now()).'U'.$new_user->id; // Many other solution
        $teacher->user = $new_user->id;
        $teacher->school = $school->id;
        $teacher->save();

        $data = [
            'email' => $request->email,
            'role'  => 'Teacher',
            'password'   => $password,
            'sch'   => $school->name,
            'schid' => $school->code
        ];

        $email = Mail::to($request->email)->send(new inviteEmail($data));

        $response = [
            'success'   => true,
        ];
        
        return response($response, 200);
    }

    public function inviteStudent(Request $request)
    {
        $rules = [
            'email'     => 'required|email|min:8|max:50|unique:users,email',
        ];

        $validator = Validator::make($request->all(), $rules);
  
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        if ($request->user()->role!='adm') {
            return response(403);
        }
        
        $school = Admin::select('school')->where('user',$request->user()->id)->first();
        $school = json_decode($school)->school;
        $school = School::find($school);

        $password = substr(md5(microtime()),rand(0,5),12);
        $new_user = new User;
        $new_user->email = strtolower($request->email);
        $new_user->password = Hash::make($password);
        $new_user->email_verified_at = \Carbon\Carbon::now();
        $new_user->role = 'std';
        $new_user->save();
        
        $student = new Student;
        $student->code = 'C'.strtotime(now()).'U'.$new_user->id; // Many other solution
        $student->user = $new_user->id;
        $student->school = $school->id;
        $student->save();

        $data = [
            'email' => $request->email,
            'role'  => 'Student',
            'password'   => $password,
            'sch'   => $school->name,
            'schid' => $school->code
        ];

        $email = Mail::to($request->email)->send(new inviteEmail($data));

        $response = [
            'success'   => true,
        ];
        
        return response($response, 200);
    }

    function getTeacherList (Request $request) {
        if($request->user()->role==='std') {
            $school = Student::select('school')->where('user',$request->user()->id)->first();
        } elseif ($request->user()->role==='tch') {
            $school = Teacher::select('school')->where('user',$request->user()->id)->first();
        } else {
            $school = Admin::select('school')->where('user',$request->user()->id)->first();
        }
        $schid = json_decode($school)->school;
        $teacher = Teacher::select('users.email', 'teachers.*')->where('school',$schid)->where('user','!=',$request->user()->id)->join('users', 'teachers.user', '=', 'users.id')->get();
        return response($teacher, 200);
    }

    function getStudentList (Request $request) {
        if($request->user()->role==='std') {
            $school = Student::select('school')->where('user',$request->user()->id)->first();
        } elseif ($request->user()->role==='tch') {
            $school = Teacher::select('school')->where('user',$request->user()->id)->first();
        } else {
            $school = Admin::select('school')->where('user',$request->user()->id)->first();
        }
        $schid = json_decode($school)->school;
        $student = Student::select('users.email', 'students.*')->where('user','!=',$request->user()->id)->where('school',$schid)->join('users', 'students.user', '=', 'users.id')->get();
        return response($student, 200);
    }

    function deleteTeacher (Request $request) {
        Teacher::where('id', $request->id)->delete();
        return response(200);
    }

    function deleteStudent (Request $request) {
        Student::where('id', $request->id)->delete();
        return response(200);
        
    }
}