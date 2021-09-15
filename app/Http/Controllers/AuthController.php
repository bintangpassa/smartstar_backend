<?php
  
namespace App\Http\Controllers;
  
use Illuminate\Http\Request;
  
// use Illuminate\Support\Facades\Auth;
use Validator;
use Hash;
// use Session;
use App\Models\User;
use App\Models\School;
use App\Models\Admin;
  
  
class AuthController extends Controller
{
    public function login(Request $request)
    {
        $rules = [
            'email'                 => 'required|email',
            'password'              => 'required|string'
        ];
  
        $messages = [
            'email.required'        => 'Email required',
            'email.email'           => 'Email invalid',
            'password.required'     => 'Password required',
            'password.string'       => 'Password must be a string'
        ];
  
        $validator = Validator::make($request->all(), $rules, $messages);
  
        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }
        
        $user= User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response([
                'success'   => false,
                'message' => ['These credentials do not match our records.']
            ], 401);
        }
    
        $token = $user->createToken('LoginAPI')->plainTextToken;
    
        $response = [
            'success'   => true,
            'user'      => $user,
            'token'     => $token
        ];
        
        return response($response, 200);
    }

    public function register(Request $request)
    {
        $rules = [
            'email'                 => 'required|email|min:8|max:50|unique:users,email',
            'password'              => 'required|confirmed|min:5|max:50',
            'schname'               => 'required|min:5|max:80|unique:schools,name',
            'code'                  => 'required|min:5|max:10',
        ];

        $messages = [
            'email.required'        => 'Email required',
            'email.email'           => 'Email not valid',
            'email.unique'          => 'Email already registerd',
            'password.required'     => 'Password required',
            'password.confirmed'    => 'Password did not match',
            'schname.required'      => 'School name required',
            'schname.min'           => 'School name must be at least 5 characters.',
            'schname.max'           => 'School name must not be greater than 80 characters.',
            'schname.unique'        => 'The school name has already been taken.'
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
  
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        
        if ($request->code!='smartstar') {
            $data = [
                'code' => ['Your code is invalid']
            ];
            return response()->json($data, 400);
        }

        $user = new User;
        
        $user->email = strtolower($request->email);
        $user->password = Hash::make($request->password);
        $user->email_verified_at = \Carbon\Carbon::now();
        $user->role = 'adm';
        $store_user = $user->save();
  
        if ($store_user) {
            $school = new School();
            $school->code = 'S'.strtotime(now()).'U'.$user->id; // Many other solution
            $school->name = strtolower($request->schname);
            $store_school = $school->save();

            if ($store_school) {
                $admin = new Admin();
                $admin->code = 'A'.strtotime(now()).'U'.$user->id; // Many other solution
                $admin->user = $user->id;
                $admin->school = $school->id;
                $store_admin = $admin->save();
                if ($store_admin) {
                    $token = $user->createToken('LoginAPI')->plainTextToken;
                    return response()->json([
                        'success' => true,
                        'user'    => $user,
                        'token'   => $token
                    ], 201);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to save Admin',
                    ], 409);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to save School',
                ], 409);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save User',
            ], 409);
        }
    }
  
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success'    => true
        ], 200);
    }
}