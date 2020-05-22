<?php

namespace App\Http\Controllers;

use App\User;
use DB;
use App\Intro;
use App\Slides;
use App\TimeQuizz;
use App\UserAnswers;
use App\CompletedQuiz;
use App\Answers;
use App\Category;
use App\CompletedTraining;
use App\Question;
use App\Quizzes;
use App\Training;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\TypeTraining;

class UserRankController extends Controller
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
                $allusers = User::where('company_id', $user->company_id)->paginate(12);
                if ($allusers) {
                    $getTrainings = null;
                    foreach ($allusers as $training) {
                        if ($getTrainings == null) $getTrainings = Training::get();
                        //else $getTrainings = $getTrainings->orWhere('id', $training->id);
                        if ($getTrainings) {
                            $rating = $training->completedTraining()->count() / $getTrainings->count() * 100;
                            //$rating["Rating precent"] = $rating;
                            $training['training_precent'] = round($rating, 0);
                        } else {
                            return response()->json(null, 200);
                        }
                        $training['quiz_precent'] = null;
                        $quizzes = Quizzes::get();
                        if ($quizzes) {
                            $prequiz = $training->completedQuiz()->count() / $quizzes->count() * 100;
                            $training['quiz_precent'] = round($prequiz, 0);
                        } else {
                            return response()->json(null, 200);
                        }
                        $training['top_ranked_users'] = null;
                        $topranked = ($training->completedQuiz()->count() + $training->completedTraining()->count()) / ($quizzes->count() + $training->count()) * 100;
                        $training['top_ranked_users'] = round($topranked, 0);
                    }
                    $col = collect($allusers['top_ranked_users']);
                    $sorted = $col->sortBy('top_ranked_users');
                    return response()->json($sorted, 200);
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id = null)
    {

        if (Auth::check()) {
            $user = Auth::user();
            if ($user) {
                $usersById = User::where('company_id', $user->company_id)->find($id);
                if ($usersById) {
                    $getTrainings = null;
                    if ($getTrainings == null) $getTrainings = Training::get();
                    if ($getTrainings) {
                        $trainingCompleted = $usersById->completedTraining()->count() / $getTrainings->count() * 100;
                        $usersById['completed_trainings'] = round($trainingCompleted, 1);
                    } else {
                        return response()->json(null, 200);
                    }

                    // Intro uvek mora da bude type_id = null

                    $intros = Intro::get();
                    if ($intros)
                        foreach ($intros as $intro)
                            $slides = Slides::where('intro_id', $intro->id)->get();
                    if ($slides) {
                        $introCompleted = $slides->count() == 0 ? 0 : ($usersById->completedSlides()->where('intro_id', $intro->id)->count() / $slides->count()) * 100;
                        $usersById['completed_intro'] = round($introCompleted, 1);
                    } else {
                        return response()->json(null, 200);
                    }
                    /* $categories = TypeTraining::get();
                    $group = $categories->groupBy('category_id');
                    $usersById['trainings_procent'] = $group; */
                    $categories = Category::get();
                    if ($categories)
                        foreach ($categories as $category) {
                            $scoreQuizzes = array();
                            $types = TypeTraining::where('category_id', $category->id)->get();
                            $counter = 0;
                            $counterCompleted = 0;
                            foreach ($types as $type) {
                                $quizzes = Quizzes::where('type_id', $type->id)->get();
                                $counter += $quizzes->count();
                                foreach ($quizzes as $quizz) {
                                    if ($quizz->completedQuiz->count()) {
                                        $counterCompleted++;
                                    }
                                    $exacts = 0;
                                    $incorrect = 0;
                                    $questions = Question::where('quizz_id', $quizz->id)->get();
                                    if ($questions)
                                        foreach ($questions as $question) {
                                            $userAnswers = $question->useransweredonquestion;
                                            if ($userAnswers) {
                                                // prvo proveravam da li je na ovo pitanje odgovorio (ako je drag adn drop onda 
                                                // mora da postoji odgovor za svaki item)
                                                if ($question->type == 2) {
                                                    $anwers = Answers::where('question_id', $question->id)->get();
                                                    if ($anwers)
                                                        $poenDragAndDrop = 1;
                                                    foreach ($anwers as $answer) {
                                                        $exist = false;
                                                        foreach ($userAnswers as $userAnswer) {
                                                            if ($userAnswer->answers_id == $answer->id) {
                                                                $exist = true;
                                                                if ($answer->exact != $userAnswer->user_response) {
                                                                    $poenDragAndDrop = 0;
                                                                }
                                                            }
                                                        }
                                                        if (!$exist) $poenDragAndDrop = 0;
                                                    }
                                                    $exacts += $poenDragAndDrop;
                                                } else if ($question->type == 1) { // choice
                                                    foreach ($userAnswers as $userAnswer) {
                                                        // return response()->json($userAnswer);
                                                        $answer = DB::table('answers')->find($userAnswer->answers_id);
                                                        if ($question->type == 1) {
                                                            if ($answer->exact) {
                                                                $exacts++;
                                                            } else {
                                                                $incorrect++;
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }

                                    $body["name"] = $quizz->name;
                                    $body["score"] = ($questions->count() ? $exacts / $questions->count() : 0) * 100;
                                    array_push($scoreQuizzes, $body);
                                    //reverse() za obrnuti skor
                                    $scoreQuizzes = collect($scoreQuizzes)->sortBy('score')->reverse()->toArray();
                                    $scoreQuizzes = array_slice($scoreQuizzes, 0, 5, false);
                                }
                            }
                            //$body["correct_answers"] = ($exacts ? $exacts / $questions->count() : 0) * 100;
                            //$body["incorrect_answers"] = $incorrect;
                            $category['score_quizzes'] = $scoreQuizzes;
                            $category['number_quizzes'] = $counter;
                            $category['completed_quiz'] = round($counter == 0 ? 0 : ($counterCompleted / $counter) * 100, 1);
                        }


                    /* $category['quiz_precent'] = null;
                    $quizzes = Quizzes::get();
                    if ($quizzes) {
                        $prequiz = $usersById->completedQuiz()->count() / $quizzes->count() * 100;
                    }
                    $category['quiz_precent'] = round($prequiz, 0); */

                    $usersById['categories'] = $categories;
                    return response()->json($usersById, 200);
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


    public function quizzRank($id = null, $category_id = null)
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user) {
                $category = Category::where('id', $category_id)->get();
                if ($category) {
                    foreach ($category as $cat) {
                        $type = TypeTraining::where('category_id', $cat->id)->get();
                        if ($type)
                            foreach ($type as $typ) {
                                $usersById = User::where('company_id', $user->company_id)->find($id);
                                if ($usersById) {
                                    $completed_quizzes = array(); //->where('type_id', $typ->id);
                                    foreach ($usersById->completedQuiz as $item) {
                                        if ($item->type_id == $typ->id) {
                                            array_push($completed_quizzes, $item);
                                        }
                                    }
                                    foreach ($completed_quizzes as $quizz) {
                                        $time = TimeQuizz::where('quizz_id', $quizz->id)->first();
                                        if ($time)
                                            $quizz['category'] = $cat->name;
                                        $quizz['time_spent'] = null;
                                        $quizz['correct'] = null;
                                        $quizz['incorrect'] = null;
                                        if ($time) {
                                            $exacts = 0;
                                            $incorrect = 0;
                                            $questions = Question::where('quizz_id', $quizz->id)->get();
                                            if ($questions)
                                                foreach ($questions as $question) {
                                                    $userAnswers = $question->useransweredonquestion;
                                                    if ($userAnswers) {
                                                        // prvo proveravam da li je na ovo pitanje odgovorio (ako je drag adn drop onda 
                                                        // mora da postoji odgovor za svaki item)
                                                        if ($question->type == 2) {
                                                            $anwers = Answers::where('question_id', $question->id)->get();
                                                            if ($anwers)
                                                                $poenDragAndDrop = 1;
                                                            foreach ($anwers as $answer) {
                                                                $exist = false;
                                                                foreach ($userAnswers as $userAnswer) {
                                                                    if ($userAnswer->answers_id == $answer->id) {
                                                                        $exist = true;
                                                                        if ($answer->exact != $userAnswer->user_response) {
                                                                            $poenDragAndDrop = 0;
                                                                        }
                                                                    }
                                                                }
                                                                if (!$exist) $poenDragAndDrop = 0;
                                                            }
                                                            $exacts += $poenDragAndDrop;
                                                        } else if ($question->type == 1) { // choice
                                                            foreach ($userAnswers as $userAnswer) {
                                                                // return response()->json($userAnswer);
                                                                $answer = DB::table('answers')->find($userAnswer->answers_id);
                                                                if ($question->type == 1) {
                                                                    if ($answer->exact) {
                                                                        $exacts++;
                                                                    } else {
                                                                        $incorrect++;
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            $timeFormat = strtotime($time["end_time"]) - strtotime($time["start_time"]);
                                            $quizz['correct'] = round($exacts == 0 ? 0 : ($exacts / $questions->count()) * 100, 1);
                                            $quizz['incorrect'] = round($incorrect == 0 ? 0 : ($incorrect / $questions->count()) * 100, 1);
                                            $quizz['time_spent'] = date($timeFormat) / 60 . " min";
                                            $quizz['category'] = $cat->name;
                                        }
                                    }
                                } else {
                                    return response()->json(array('error_type' => 'unauthorized', 'code' => 401, 'errorr_message' => 'unauthorized'), 401);
                                }
                            }
                        $user['completed_quiz'] = $completed_quizzes;
                    }
                    return response()->json($user, 200);
                } else {
                    return response()->json(array('error_type' => 'unauthorized', 'code' => 401, 'errorr_message' => 'Unauthorized'), 401);
                }
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
    public function edit($id)
    {
        //
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
    public function destroy($id)
    {
        //
    }
}
