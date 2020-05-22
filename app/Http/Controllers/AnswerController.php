<?php

namespace App\Http\Controllers;

use App\User;
use App\Question;
use Illuminate\Support\Facades\Input;
use App\Answers;
use DB;
use App\UserAnswers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AnswerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Auth::check()) {
            $answer = Answers::with('question')->get();
            if ($answer) {
                return response()->json($answer, 200);
            } else {
                return response()->json(array('error_type' => 'no data', 'code' => 404, 'errorr_message' => 'No data'), 404);
            }
        } else {
            return response()->json(array('error_type' => 'not_logged_in', 'code' => 401, 'errorr_message' => 'Not logged in'), 401);
        }
    }

    // *
    public function useranswered(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user) {
                $array = $request->all();
                foreach ($array["answers"] as $row) {
                    UserAnswers::create([
                        'answers_id'      => $row["answers_id"],
                        'question_id'   => $row["question_id"],
                        'user_response'      => 1,
                        'user_id'       => $user->id,
                    ]);
                }
                $exactanswer = Answers::where('question_id', $request->answers[0]["question_id"])->where('exact', 1)->get();
                return response()->json($exactanswer, 200);
            } else {
                return response()->json(array('error_type' => 'unauthorized', 'code' => 401, 'errorr_message' => 'Unauthorized'), 401);
            }
        } else {
            return response()->json(array('error_type' => 'not_logged_in', 'code' => 401, 'errorr_message' => 'Not logged in'), 401);
        }
    }


    public function answer($id = null)
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user) {
                $answers = UserAnswers::where('question_id', $id)->first();
                if ($answers) {
                    return response()->json($answers, 200);
                } else {
                    return response()->json(array('error_type' => 'no_data', 'code' => 404, 'errorr_message' => 'No data'), 404);
                }
            } else {
                return response()->json(array('error_type' => 'unauthorized', 'code' => 401, 'errorr_message' => 'Unauthorized'), 401);
            }
        } else {
            return response()->json(array('error_type' => 'not_logged_in', 'code' => 401, 'errorr_message' => 'Not logged in'), 401);
        }
    }



    public function getallanswers($id = null)
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user) {
                $questions = Question::find($id);
                if ($questions->type == 1) {
                    $questions['answers'] = Answers::where('question_id', $id)->get();
                    return response()->json($questions, 200);
                } else if ($questions->type == 2) {
                    $questions['answers_left_box'] = Answers::where('question_id', $id)->where('exact', 0)->get();
                    $questions['answers_right_box'] = Answers::where('question_id', $id)->where('exact', 1)->get();
                    return response()->json($questions, 200);
                } else {
                    return response()->json(array('error_type' => 'no_data', 'code' => 404, 'errorr_message' => 'No data'), 404);
                }
            } else {
                return response()->json(array('error_type' => 'unauthorized', 'code' => 401, 'errorr_message' => 'Unauthorized'), 401);
            }
        } else {
            return response()->json(array('error_type' => 'not_logged_in', 'code' => 401, 'errorr_message' => 'Not logged in'), 401);
        }
    }


    // SUPERADMIN GETS
    public function adminGetAllAnswers($id = null)
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user) {
                $questions = Question::find($id);
                if ($questions->type == 1) {
                    $questions['answers'] = Answers::where('question_id', $id)->get();
                    return response()->json($questions, 200);
                } else if ($questions->type == 2) {
                    $questions['answers_left_box'] = Answers::where('question_id', $id)->where('exact', 0)->get();
                    $questions['answers_right_box'] = Answers::where('question_id', $id)->where('exact', 1)->get();
                    return response()->json($questions, 200);
                } else {
                    return response()->json(array('error_type' => 'no_data', 'code' => 404, 'errorr_message' => 'No data'), 404);
                }
            } else {
                return response()->json(array('error_type' => 'unauthorized', 'code' => 401, 'errorr_message' => 'Not loggedin'), 401);
            }
        } else {
            return response()->json(array('error_type' => 'not_logged_in', 'code' => 401, 'errorr_message' => 'Not logged in'), 401);
        }
    }

    /* public function checkAnswer(Request $request)
    {
        $data = Input::all();
        //return response()->json($request->answers_id);
        if (Auth::check()) {
            $answer = DB::table('answers')->find($request->answers_id);
            if ($answer) {
                return response()->json($answer, 200);
            } else {
                return response()->json('error', 401);
            }
        } else {
            return response()->json('error', 401);
        }
    }*/

    public function exactanswer($id = null)
    {
        if (Auth::check()) {
            $exact = Answers::where('question_id', $id)->where('exact', 1)->get();
            if ($exact) {
                return response()->json($exact, 200);
            } else {
                return response()->json(array('error_type' => 'no_data', 'code' => 404, 'errorr_message' => 'No data'), 404);
            }
        } else {
            return response()->json(array('error_type' => 'not_logged_in', 'code' => 401, 'errorr_message' => 'Not logged in'), 401);
        }
    }


    // SUPERADMIN GETS
    public function adminExactAnswer($id = null)
    {
        if (Auth::check()) {
            $exact = Answers::where('question_id', $id)->where('exact', 1)->get();
            if ($exact) {
                return response()->json($exact, 200);
            } else {
                return response()->json(array('error_type' => 'no_data', 'code' => 404, 'errorr_message' => 'No data'), 404);
            }
        } else {
            return response()->json(array('error_type' => 'not_logged_in', 'code' => 401, 'errorr_message' => 'Not logged in'), 401);
        }
    }



    public function userAnsweredDragAndDrop(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user) {
                $array = $request->all();
                foreach ($array["data"] as $row) {
                    UserAnswers::create([
                        'answers_id'      => $row["answers_id"],
                        'question_id'   => $row["question_id"],
                        'user_response'      => $row["user_response"],
                        'user_id'       => $user->id,
                    ]);
                }
                $exactanswer['answers_left_box'] = Answers::where('question_id', $request->data[0]["question_id"])->where('exact', 0)->get();
                $exactanswer['answers_right_box'] = Answers::where('question_id', $request->data[0]["question_id"])->where('exact', 1)->get();
                if ($exactanswer) {
                    return response()->json($exactanswer, 200);
                } else {
                    return response()->json(array('error_type' => 'no data', 'code' => 401, 'errorr_message' => 'No data'), 401);
                }
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
    public function create(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->roles_id == 3) {
                $name = $request->name;
                $exact = $request->exact;
                $question_id = $request->question_id;
                $answers = new Answers;
                $answers->name = $name;
                $answers->exact = $exact;
                $answers->question_id = $question_id;
                $answers->save();
                return response()->json($answers, 200);
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
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user) {
                $answer = Answers::with('question')->findOrFail($id);
                if ($answer) {
                    return response()->json($answer, 200);
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
    /*   public function edit(Request $request, $id)
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user) {
                $answer = Answers::find($id);
                if ($request->name) $answer->name = $request->name;
                if ($request->exact) $answer->exact = $request->exact;
                if ($request->question_id) $answer->question_id = $request->question_id;
                $answer->save();
                return response()->json("message: success");
            } else {
                return response()->json('error', 401);
            }
        } else {
            return response()->json('error', 401);
        }
    }
    */


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

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id = null)
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user) {
                $answer = Answers::find($id);
                if ($answer) {
                    $answer->delete();
                    return response()->json($answer, 200);
                } else {
                    return response()->json(array('error_type' => 'no data', 'code' => 401, 'errorr_message' => 'No data'), 401);
                }
            } else {
                return response()->json(array('error_type' => 'unauthorized', 'code' => 401, 'errorr_message' => 'Unauthorized'), 401);
            }
        } else {
            return response()->json(array('error_type' => 'not_logged_in', 'code' => 401, 'errorr_message' => 'Not logged in'), 401);
        }
    }
}
