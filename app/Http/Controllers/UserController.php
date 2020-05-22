<?php

namespace App\Http\Controllers;

use Validator;
use App\User;
use App\Classes\Utils;
use App\Sector;
use App\Companys;
use Illuminate\Http\Request;
use Illuminate\Http\Testing\MimeType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;
use Image;

class UserController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getMe(Request $request)
    {
        if (Auth::check()) {
            $check = Auth::user();
            if ($check) {
                $checkArr = $check->toArray();
                $sector = Sector::where('id', $check->sector_id)->get(['name']);
                if ($sector) {
                    $checkArr['sector'] = $sector;
                } else {
                    return response()->json(null, 200);
                }
                $company = Companys::where('id', $check->company_id)->get(['name']);
                if ($company) {
                    $checkArr['company'] = $company;
                } else {
                    return response()->json(null, 200);
                }
                return response()->json($checkArr, 200);
            } else {
                return response()->json(array('error_type' => 'unauthorized', 'code' => 401, 'message' => 'Unauthorized'), 401);
            }
        } else {
            return response()->json(array('error_type' => 'not_logged_in', 'code' => 401, 'message' => 'Not loggedin'), 401);
        }
    }



    public function edit(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();
            if($user){
                if ($request->name) $user->name = $request->name;
                if ($request->email) $user->email = $request->email;
                if ($request->password) $user->password = $user->password;
                if ($request->company_id) $user->company_id = $request->company_id;
                if ($request->icon) $user->icon = $request->icon;
                if ($request->position) $user->position = $request->position;
                if ($request->gender) $user->gender = $request->gender;
                if ($request->sector_id) $user->sector_id = $request->sector_id;
                $user->save();
                return response()->json($user, 200);
            } else {
                return response()->json(array('error_type' => 'unauthorized', 'code' => 401, 'message' => 'Unauthorized'), 401);
            }
        } else {
            return response()->json(array('error_type' => 'not_logged_in', 'code' => 401, 'message' => 'Not loggedin'), 401);
        }
}



// SUPERADMIN EDIT
    public function editUser(Request $request, $id = null)
    {
        if (Auth::check()) {
            $user = Auth::user();
            if($user){
            $users = User::find($id);
            if ($users) {
                if ($request->name) $users->name = $request->name;
                if ($request->email) $users->email = $request->email;
                if ($request->password) $users->password = $user->password;
                if ($request->company_id) $users->company_id = $request->company_id;
                if ($request->icon) $users->icon = $request->icon;
                if ($request->position) $users->position = $request->position;
                if ($request->gender) $users->gender = $request->gender;
                if ($request->sector_id) $users->sector_id = $request->sector_id;
                $users->save();
                return response()->json($users, 200);
            } else {
                return response()->json(array('error_type' => 'unauthorized', 'code' => 401, 'message' => 'Unauthorized'), 401);
            }
        } else {
            return response()->json(array('error_type' => 'not_logged_in', 'code' => 401, 'message' => 'Not loggedin'), 401);
        }
    } else {
        return response()->json(array('error_type' => 'not_logged_in', 'code' => 401, 'message' => 'Not loggedin'), 401);
    }
}


    // SUPERADMIN GETS
    public function getUsers($id = null)
    {
        if (Auth::check()) {
            $users = User::where('company_id', $id)->get();
            if (!$users->isEmpty()) {
                return response()->json($users, 200);
            } else {
                return response()->json(array('error_type' => 'No data', 'code' => 404, 'error_message' => 'No data'), 404);
            }
        } else {
            return response()->json('error', 401);
        }
    }



    public function showUserIcon(Request $request, $media)
    {
        if (File::exists(app_path("user_icon/$media"))) {
            return response()->file(app_path("user_icon/$media"), [
                'Content-Type' => 'image/jpg',
                'Content-Disposition' => 'inline; filename="image/jpg"'
            ]);
        } else {
            return response()->json(array('error_type' => 'missing_inputs', 'code' => 422, 'error_message' => 'No file.'), 422);
        }
    }


    public function saveUserIcon(Request $request)
    {
        $inputFile = $request->file('file');
        if ($inputFile) {
            $random_name = str_random(8);
            $extension = strtolower($inputFile->getClientOriginalExtension());
            $filenameNew = 'userIcon' . $random_name . '.' . $extension;
            $moveImg = $inputFile->move(app_path() . '/user_icon/', $filenameNew);
            if ($moveImg) {
                $url = Utils::$MAINURL_address . '/usericon/' . $filenameNew;
                if (File::exists(app_path("user_icon/" . $filenameNew))) {
                    return response()->json($url, 200);
                } else {
                    return response()->json(['status' => 421, 'result' => app_path("XLS/import/" . $filenameNew)]);
                }
            } else {
                return response()->json(array('error_type' => 'missing_inputs', 'code' => 422, 'error_message' => 'No file.'), 422);
            }
        } else {
            return response()->json(array('error_type' => 'missing_inputs', 'code' => 422, 'error_message' => 'No file.'), 422);
        }
    }

    public function deleteUser($id = null)
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user) {
                $users = User::find($id);
                if ($users->roles_id == 3) {
                    $users->delete();
                }
                return response()->json($users, 200);
            } else {
                return response()->json(array('error_type' => 'unauthorized', 'code' => 401, 'error_message' => 'Unauthorized'), 401);
            }
        } else {
            return response()->json(array('error_type' => 'not_logged_in', 'code' => 401, 'errorr_message' => 'Not loggedin'), 401);
        }
    }
}
