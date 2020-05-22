<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Intro;
use PDO;

class IntroController extends Controller
{

    // Get Intro
    public function getIntro()
    {
        if (Auth::user()) {
            $user = Auth::user();
            if ($user) {
                $intro = Intro::where('company_id', $user->company_id)->get();
                if ($intro) {
                    return response()->json($intro, 200);
                } else {
                    return response()->json(array('error_type' => 'no data', 'code' => 404, 'errorr_message' => 'No data'), 404);
                }
            } else {
                return response()->json(array('error_type' => 'unauthorized', 'code' => 401, 'errorr_message' => 'Unauthorized'), 401);
            }
        } else {
            return response()->json(array('error_type' => 'not_logged_in', 'code' => 401, 'errorr_message' => 'Not logged in'), 401);
        }
    }
    /*
    public function getIntroSuperAdmin($id = null) {
        if(Auth::user()) {
            $user = Auth::user();
            if($user){
                $intro = Intro::where('company_id', $id)->get();
                if($intro){
                    return response()->json($intro, 200);
                } else {
                    return response()->json('intro problem', 401);
                }
            } else {
                return response()->json('user problem', 401);
            }
        } else {
            return response()->json('user nije authorizovan ', 200);
        }
    }
    */


    // Delete Intro 
    public function deleteIntro($id = null)
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user) {
                $intro = Intro::find($id);
                if ($user->roles_id == 3) {
                    $intro->delete();
                } else {
                    return response()->json(array('error_type' => 'unauthorized', 'code' => 401, 'errorr_message' => 'Unauthorized'), 401);
                }
                return response()->json($intro, 200);
            } else {
                return response()->json(array('error_type' => 'unauthorized', 'code' => 401, 'errorr_message' => 'Unauthorized'), 401);
            }
        } else {
            return response()->json(array('error_type' => 'not_logged_in', 'code' => 401, 'errorr_message' => 'Not logged in'), 401);
        }
    }

    // Create Intro 
    public function createIntro(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user) {
                $title = $request->title;
                $tekst = $request->tekst;
                $company_id = $request->company_id;
                $check = Intro::where('company_id', $request->company_id)->get()->count();
                if ($check < 1) {
                    $intro = new Intro;
                    $intro->title = $title;
                    $intro->tekst = $tekst;
                    $intro->company_id = $company_id;
                    $intro->save();
                    return response()->json($intro, 200);
                } else {
                    return response()->json(array('error_type' => 'duplicate', 'code' => 401, 'errorr_message' => 'Only one intro per company!'), 401);
                }
            } else {
                return response()->json(array('error_type' => 'unauthorized', 'code' => 401, 'errorr_message' => 'Unauthorized'), 401);
            }
        } else {
            return response()->json(array('error_type' => 'not_logged_in', 'code' => 401, 'errorr_message' => 'Not logged in'), 401);
        }
    }

    // Edit Intro
    public function edit(Request $request, $id = null)
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user) {
                $intro = Intro::find($id);
                if ($request->title) $intro->title = $request->title;
                if ($request->tekst) $intro->tekst = $request->tekst;
                $intro->save();
                return response()->json($intro, 200);
            } else {
                return response()->json(array('error_type' => 'unauthorized', 'code' => 401, 'errorr_message' => 'Unauthorized'), 401);
            }
        } else {
            return response()->json(array('error_type' => 'not_logged_in', 'code' => 401, 'errorr_message' => 'Not logged in'), 401);
        }
    }
}
