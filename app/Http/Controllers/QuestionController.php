<?php

namespace App\Http\Controllers;

use DB;
use App\Answers;
use App\Quizzes;
use App\Question;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class QuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function all()
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user) {
                $questions = Question::all();
                if ($questions) {
                    return response()->json($questions, 200);
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

    public function index($id = null)
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user) {
                $questions = Question::where('quizz_id', $id)->with('answers')->get();
                if ($questions) {
                    return response()->json($questions, 200);
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


    // SUPERADMIN GETS
    public function adminQuizzesQuestions($id = null)
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user) {
                $questions = Question::where('quizz_id', $id)->with('answers')->get();
                if ($questions) {
                    return response()->json($questions, 200);
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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
                $check = Question::where('question', $request->question)->get();
                if ($check && count($check) == 0) {
                    $type = $request->type;
                    $question = $request->question;
                    $quizz_id = $request->quizz_id;
                    $insert = new Question;
                    $insert->type = $type;
                    $insert->question = $question;
                    $insert->quizz_id = $quizz_id;
                    $insert->save();
                    $answers = $request->get('answers');
                    if ($answers) {
                        foreach ($answers as $answer) {
                            $answer['question_id'] = $insert->id;
                            $answer['name'];
                            $answer['exact'];
                            $insert->questionAnswers()->insert($answer);
                        }
                    }
                    return response()->json(array($insert, $answers), 200);
                } else {
                    return response()->json('Question with that name already exists!', 401);
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
            $question = Question::with('quizz')->findOrFail($id);
            if ($question) {
                return response()->json($question, 200);
            } else {
                return response()->json(array('error_type' => 'no data', 'code' => 404, 'errorr_message' => 'No data'), 404);
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
                $question = Question::find($id);
                if ($request->type) $question->type = $request->type;
                if ($request->question) $question->question = $request->question;
                if ($request->quizz_id) $question->quizz_id = $request->quizz_id;
                if ($request->quizz_id) $question->description = $request->description;
                $question->save();
                $oldAnswers = Answers::where('question_id', $id)->get();
                if ($oldAnswers) {
                    foreach ($oldAnswers as $old) {
                        $old->delete();
                    }
                }
                $answers = $request->get('answers');
                if ($answers) {
                    foreach ($answers as $answer) {
                        $answer['question_id'] = $id;
                        $answer['name'];
                        $answer['exact'];
                        $question->answers()->insert($answer);
                    }
                    return response()->json("Updated", 200);
                }
            } else {
                return response()->json(array('error_type' => 'unauthorized', 'code' => 401, 'errorr_message' => 'Unauthorized'), 401);
            }
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
                $question = Question::find($id);
                if ($question) {
                    $question->delete();
                    return response()->json($question, 200);
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
