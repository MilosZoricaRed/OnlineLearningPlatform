<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use DB;
use Illuminate\Support\Carbon;
use App\User;
use App\Company;
use App\TimeQuizz;
use App\CompletedQuiz;
use App\Quizzes;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class QuizController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user) {
                $quizzes = Quizzes::where('company_id', $user->company_id)->where('published', '1')->with('company')->get();
                if ($quizzes) {
                    foreach ($quizzes as $quiz) {
                        if ($user->completedQuiz()->find($quiz->id)) {
                            $quiz["status"] = "completed";
                        } else if ($user->completedTraining()->find($quiz->training_id)) {
                            $quiz["status"] = "available";
                        } else {
                            $quiz["status"] = "not available";
                        }
                    }
                }
                //return response()->json($user->completedQuiz()->find($quiz->id));
                return response()->json($quizzes, 200);
            } else {
                return response()->json(array('error_type' => 'unauthorized', 'code' => 401, 'errorr_message' => 'Unauthorized'), 401);
            }
        } else {
            return response()->json(array('error_type' => 'not_logged_in', 'code' => 401, 'errorr_message' => 'Not logged in'), 401);
        }
    }






    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    public function completed(Request $request, $id = null)
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user) {
                $check = CompletedQuiz::where('quizz_id', $id)->where('user_id', $user->id)->get();
                if ($check && count($check) == 0) {
                    $completed = new CompletedQuiz;
                    $completed->user_id = $user->id;
                    $completed->quizz_id = $id;
                    $completed->save();
                    return response()->json("created: success", 200);
                } else {
                    return response()->json("quiz completed", 401);
                }
            } else {
                return response()->json(array('error_type' => 'unauthorized', 'code' => 401, 'errorr_message' => 'Unauthorized'), 401);
            }
        } else {
            return response()->json(array('error_type' => 'not_logged_in', 'code' => 401, 'errorr_message' => 'Not logged in'), 401);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user) {
                $check = Quizzes::where('training_id', $request->training_id)->get();
                if ($check && count($check) == 0) {
                    $name = $request->name;
                    $training_id = $request->training_id;
                    $company_id = $request->company_id;
                    $type_id = $request->type_id;
                    $quiz = new Quizzes;
                    $quiz->name = $name;
                    $quiz->training_id = $training_id;
                    $quiz->company_id = $company_id;
                    $quiz->type_id = $type_id;
                    $quiz->save();
                    return response()->json($quiz, 200);
                } else {
                    return response()->json('Quiz for that training already exists!', 401);
                }
            } else {
                return response()->json(array('error_type' => 'unauthorized', 'code' => 401, 'errorr_message' => 'Unauthorized'), 401);
            }
        } else {
            return response()->json(array('error_type' => 'not_logged_in', 'code' => 401, 'errorr_message' => 'Not logged in'), 401);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id = null)
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user) {
                $quizes = Quizzes::where('company_id', $user->company_id)->where('published', '1')->with('training')->findOrFail($id);
                if ($quizes) {
                    foreach ($quizes as $quiz) {
                        $quizes["status"] = "not complited";
                        if ($user->completedQuiz()->find($quizes->id)) {
                            $quizes["status"] = "completed";
                        }
                    }
                    return response()->json($quizes, 200);
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

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id = null)
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user) {
                $quizz = Quizzes::find($id);
                if ($request->name) $quizz->name = $request->name;
                if($request->published) $quizz->published = $request->published;
                if ($request->training_id) $quizz->training_id = $request->training_id;
                if ($request->company_id) $quizz->company_id = $request->company_id;
                if ($request->type_id) $quizz->type_id = $request->type_id;
                $quizz->save();
                return response()->json($quizz, 200);
            } else {
                return response()->json(array('error_type' => 'unauthorized', 'code' => 401, 'errorr_message' => 'Unauthorized'), 401);
            }
        } else {
            return response()->json(array('error_type' => 'not_logged_in', 'code' => 401, 'errorr_message' => 'not_logged_in'), 401);
        }
    }


    public function startTime(Request $request, $id = null)
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user) {
                //$check = Quizzes::where('id', $id)->get();
                // if ($check && count($check) == 1) {
                //$start_time = $request->start_time;
                $startTime = new TimeQuizz();
                $startTime->user_id = $user->id;
                $startTime->quizz_id = $id;
                $startTime->start_time = Carbon::now();
                $startTime->save();
                return response()->json("message: success", 200);
            } else {
                return response()->json(array('error_type' => 'unauthorized', 'code' => 401, 'errorr_message' => 'Unauthorized'), 401);
            }
        } else {
            return response()->json(array('error_type' => 'not_logged_in', 'code' => 401, 'errorr_message' => 'Not logged in'), 401);
        }
    }

    public function endTime(Request $request, $id = null)
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user) {
                $check = TimeQuizz::where('user_id', $user->id)->where('quizz_id', $id)->get();
                if ($check) {
                    $user = $user->id;
                    $end_time = Carbon::now();
                    $endTime = new TimeQuizz();
                    $endTime->end_time = $end_time;
                    DB::update('update user_time_quizz set end_time = ? where quizz_id = ? and user_id = ?', [$end_time, $id, $user]);
                    return response()->json("message: success", 200);
                } else {
                    return response()->json('error', 401);
                }
            } else {
                return response()->json(array('error_type' => 'unauthorized', 'code' => 401, 'errorr_message' => 'Unauthorized'), 401);
            }
        } else {
            return response()->json(array('error_type' => 'not_logged_in', 'code' => 401, 'errorr_message' => 'Not logged in'), 401);
        }
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }


    public function deleteCompletedQuizz($id = null)
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user) {
                $deletedQuiz = CompletedQuiz::where('id', $id)->delete();
                return response()->json(array('message' => 'Completed quizz successful delete', 'code' => 200, 'quizz' => $deletedQuiz), 200);
            } else {
                return response()->json(array('error_type' => 'unauthorized', 'code' => 401, 'errorr_message' => 'Unauthorized'), 401);
            }
        } else {
            return response()->json(array('error_type' => 'not_logged_in', 'code' => 401, 'errorr_message' => 'Not logged in'), 401);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    /*
    public function destroy($id = null)
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user) {
                DB::table('quizzes')->delete($id);
                return response()->json(array('message' => 'Quizz successful delete', 'code' => 200, 'quizz' => $id), 200);
            } else {
                return response()->json(array('error_type' => 'unauthorized', 'code' => 401, 'errorr_message' => 'Unauthorized'), 401);
            }
        } else {
            return response()->json(array('error_type' => 'not_logged_in', 'code' => 401, 'errorr_message' => 'Not logged in'), 401);
        }
    }
    */
}
