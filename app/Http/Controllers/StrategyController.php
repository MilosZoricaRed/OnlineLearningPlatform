<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Strategy;

class StrategyController extends Controller
{

    // Get Strategy
    public function getStrategy()
    {
        if (Auth::user()) {
            $user = Auth::user();
            if ($user) {
                $strategy = Strategy::where('company_id', $user->company_id)->get();
                if ($strategy) {
                    return response()->json($strategy, 200);
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

    public function deleteStrategy($id = null)
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user) {
                $strategy = Strategy::find($id);
                $strategy->delete();
                return response()->json('Strategy deleted!', 200);
            } else {
                return response()->json(array('error_type' => 'unauthorized', 'code' => 401, 'errorr_message' => 'Unauthorized'), 401);
            }
        } else {
            return response()->json(array('error_type' => 'not_logged_in', 'code' => 401, 'errorr_message' => 'Not logged in'), 401);
        }
    }

    // Create Strategy
    public function createStrategy(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user) {
                $title = $request->title;
                $tekst = $request->tekst;
                $company_id = $request->company_id;
                $company_id = $request->company_id;
                $check = Strategy::where('company_id', $request->company_id)->get()->count();
                if ($check < 1) {
                    $strategy = new Strategy;
                    $strategy->title = $title;
                    $strategy->tekst = $tekst;
                    $strategy->company_id = $company_id;
                    $strategy->save();
                    return response()->json($strategy, 200);
                } else {
                    return response()->json(array('error_type' => 'duplicate', 'code' => 401, 'errorr_message' => 'Only one intro per company!'), 401);
                }
            } else {
                return response()->json(array('error_type' => 'unauthorized', 'code' => 401, 'errorr_message' => 'Unauthorized'), 401);
            }
        } else {
            return response()->json(array('error_type' => 'not_logged_in', 'code' => 401, 'errorr_message' => 'not_logged_in'), 401);
        }
    }

    // Edit Strategy
    public function edit(Request $request, $id = null)
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user) {
                $strategy = Strategy::find($id);
                if ($request->title) $strategy->title = $request->title;
                if ($request->tekst) $strategy->tekst = $request->tekst;
                $strategy->save();
                return response()->json($strategy, 200);
            } else {
                return response()->json(array('error_type' => 'unauthorized', 'code' => 401, 'errorr_message' => 'Unauthorized'), 401);
            }
        } else {
            return response()->json(array('error_type' => 'not_logged_in', 'code' => 401, 'errorr_message' => 'Not logged in'), 401);
        }
    }
}
