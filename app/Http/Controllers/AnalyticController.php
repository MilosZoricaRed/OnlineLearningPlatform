<?php

namespace App\Http\Controllers;

use App\CompletedQuiz;
use App\CompletedSlides;
use App\Training;
use App\CompletedTraining;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Quizzes;
use App\Sector;
use App\Slides;
use App\Strategy;
use App\TimeQuizz;
use App\TimeTraining;
use Illuminate\Cache\Repository;
use Illuminate\Support\Facades\Auth;

class AnalyticController extends Controller

// ADMIN GETS
{
    public function usersAnalytics(Request $request, $id = null)
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user) {
                $users = User::where('company_id', $user['company_id'])->get();
                if ($users) {
                    $usersCount = $users->count();

                    $trainings = Training::where('company_id', $user['company_id'])->get();
                    if ($trainings) {
                        $cntTraining = 0;
                        foreach ($trainings as $training) {
                            if ($training->trainingCompleted->count()) {
                                $cntTraining++;
                            }
                        }
                    } else {
                        return response()->json(null, 200);
                    }
                    $quizzes = Quizzes::where('company_id', $user['company_id'])->get();
                    if ($quizzes) {
                        $cntQuizzes = 0;
                        foreach ($quizzes as $quizz) {
                            if ($quizz->completedQuiz->count()) {
                                $cntQuizzes++;
                            }
                        }
                    } else {
                        return response()->json(null, 200);
                    }
                    $trainingsCheks = Training::where('company_id', $user['company_id'])->get();
                    if ($trainingsCheks) {
                        $allTimes = 0;
                        $timeFormat = 0;
                        foreach ($trainingsCheks as $cheks) {
                            $times = TimeTraining::where('training_id', $cheks->id)->get();
                            if ($times) {
                                $allTimes += $times->count();
                                foreach ($times as $time) {
                                    $timeFormat += (strtotime($time['end_time']) - strtotime($time['start_time'])) / 60;
                                }
                            }
                        }
                    } else {
                        return response()->json(null, 200);
                    }
                    $males = User::where('gender', "M")->where('company_id', $user['company_id'])->get();
                    if ($males) {
                        $toArray["male_users"] = $males->count();
                    }
                    $females = User::where('gender', "F")->where('company_id', $user['company_id'])->get();
                    if ($females) {
                        $toArray["female_users"] = $females->count();
                    }
                } else {
                    return response()->json(null, 200);
                }

                $avgTime = $timeFormat / $allTimes;
                $toArray["total_users"] = $usersCount;
                $toArray["average_time_spent"] = $avgTime;
                $toArray["total_completed_quizzes"] = $cntQuizzes;
                $toArray["total_completed_trainings"] = $cntTraining;

                return response()->json($toArray, 200);
            } else {
                return response()->json(array('error_type' => 'unauthorized', 'code' => 401, 'error_message' => 'Unauthorized'), 401);
            }
        } else {
            return response()->json(array('error_type' => 'not_logged_in', 'code' => 401, 'errorr_message' => 'Not logged in'), 401);
        }
    }


    // ADMIN GETS
    public function usersSectors($id = null)
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user) {
                $all = array();
                $i = 0;
                $sectors = Sector::where('company_id', $user['company_id'])->get();
                if ($sectors) {
                    foreach ($sectors as $sector) {
                        $users = User::where('company_id', $user['company_id'])->where('sector_id', $sector->id)->get();
                        $sector['users'] = $users->count();
                    }
                    return response()->json($sectors, 200);
                } else {
                    return response()->json(array('error_type' => 'no data', 'code' => 401, 'error_message' => 'No data'), 401);
                }
            } else {
                return response()->json(array('error_type' => 'unauthorized', 'code' => 401, 'errorr_message' => 'Unauthorized'), 401);
            }
        } else {
            return response()->json(array('error_type' => 'not_logged_in', 'code' => 401, 'errorr_message' => 'Not logged in'), 401);
        }
    }


    // ADMIN GETS
    public function postTrainingGet(Request $request, $id = null)
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user) {
                // $all = $request;
                $completedTrainings = 0;
                // $times = 0;
                $timeFormat = 0;
                $cTrainings = array();
                $allTimes = 0;
                $users['users'] = User::where('sector_id', $request['sector_id'])->where('company_id', $user['company_id'])->get();
                if ($users) {
                    foreach ($users['users'] as $us) {
                        $completedTraining = CompletedTraining::where('training_id', $request['training_id'])->where('user_id', $us->id)->get();
                        if ($completedTraining) {
                            $completedTrainings += $completedTraining->count();
                            foreach ($completedTraining as $tme) {
                                $times = TimeTraining::where('training_id', $tme->training_id)->where('user_id', $us->id)->get();
                                if ($times) {
                                    $allTimes += $times->count();
                                    foreach ($times as $tm) {
                                        $timeFormat += (strtotime($tm['end_time']) - strtotime($tm['start_time']));
                                    }
                                } else {
                                    return response()->json(null, 200);
                                }
                            }
                        } else {
                            return response()->json(null, 200);
                        }
                        $cTraining = CompletedTraining::where('training_id', $request['training_id'])->where('user_id', $us->id)->get()->toArray();
                        $cTrainings = array_merge($cTrainings, $cTraining);
                    }
                    //return response()->json();
                    $avgTime = $timeFormat == 0 ? 0 : $timeFormat / 60 / $allTimes;
                    $users['average_time_spent'] = round($avgTime) . " min";
                    $jedandan = 60 * 60 * 24;
                    $jedansat = 60 * 60;
                    $jedanmin = 60;
                    $dani = intdiv($timeFormat, $jedandan);
                    // $users['total_learning_days'] = $dani;
                    $timeFormat -= $dani * $jedandan; // ono sto je ostalo u sekundama (sati minuti sekunde)
                    $sati = intdiv($timeFormat, $jedansat);
                    // $users['total_learning_hours'] = $sati;
                    $timeFormat -= $sati * $jedansat;
                    $minuta = intdiv($timeFormat, $jedanmin);
                    // $users['total_learning_min'] = $minuta;
                    $timeFormat -= $minuta * $jedanmin;
                    $seconds =  $timeFormat;
                    //$users['total_learning_sec'] = $seconds;
                    $users['total_completed_rate'] = count($cTrainings) == 0 ? 0 : count($cTrainings) / $users['users']->count() * 100;
                    $users['total_spend_time'] = $dani . "d " . $sati . "h " . $minuta . "min " . $seconds . "sec";
                    $users['trainings_completed'] = count($cTrainings);
                    $users['users'] = $users['users']->count();
                    return response()->json($users, 200);
                } else {
                    return response()->json(array('error_type' => 'no data', 'code' => 404, 'errorr_message' => 'No data'), 404);
                }
            } else {
                return response()->json(array('error_type' => 'unauthorized', 'code' => 401, 'error_message' => 'Usnauthorized'), 401);
            }
        } else {
            return response()->json(array('error_type' => 'not_logged_in', 'code' => 401, 'errorr_message' => 'Not logged in'), 401);
        }
    }


    // ADMIN GETS
    public function postQuizzesGet(Request $request, $id = null)
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user) {
                $all = $request;
                $completedQuizzes = 0;
                $times = 0;
                $timeFormat = 0;
                $cQuizzes = array();
                $allTimes = 0;
                $users['users'] = User::where('sector_id', $request['sector_id'])->where('company_id', $user['company_id'])->get();
                if ($users) {
                    foreach ($users['users'] as $us) {
                        $completedQuizz = CompletedQuiz::where('quizz_id', $request['quizz_id'])->where('user_id', $us->id)->get();
                        if ($completedQuizz) {
                            $completedQuizzes += $completedQuizz->count();
                            foreach ($completedQuizz as $tme) {
                                $times = TimeQuizz::where('quizz_id', $tme->quizz_id)->where('user_id', $us->id)->get();
                                if ($times) {
                                    $allTimes += $times->count();
                                    foreach ($times as $tm) {
                                        $timeFormat += (strtotime($tm['end_time']) - strtotime($tm['start_time']));
                                    }
                                } else {
                                    return response()->json(null, 200);
                                }
                            }
                        } else {
                            return response()->json(null, 200);
                        }
                        $cQuizz = CompletedQuiz::where('quizz_id', $request['quizz_id'])->where('user_id', $us->id)->get()->toArray();
                        if ($cQuizz) {
                            $cQuizzes = array_merge($cQuizzes, $cQuizz);
                        } else {
                            return response()->json(null, 200);
                        }
                    }

                    $avgTime = $timeFormat == 0 ? 0 : $timeFormat / 60 / $allTimes;
                    //$users['users'] = $users;
                    $users['average_time_spent'] = round($avgTime) . " min";
                    $jedandan = 60 * 60 * 24;
                    $jedansat = 60 * 60;
                    $jedanmin = 60;
                    $dani = intdiv($timeFormat, $jedandan);
                    // $users['total_learning_days'] = $dani;
                    $timeFormat -= $dani * $jedandan; // ono sto je ostalo u sekundama (sati minuti sekunde)
                    $sati = intdiv($timeFormat, $jedansat);
                    // $users['total_learning_hours'] = $sati;
                    $timeFormat -= $sati * $jedansat;
                    $minuta = intdiv($timeFormat, $jedanmin);
                    // $users['total_learning_min'] = $minuta;
                    $timeFormat -= $minuta * $jedanmin;
                    $seconds =  $timeFormat;
                    //$users['total_learning_sec'] = $seconds;
                    $users['total_completed_rate'] = count($cQuizzes) == 0 ? 0 : count($cQuizzes) / count($users['users']) * 100;
                    $users['total_spend_time'] = $dani . "d " . $sati . "h " . $minuta . "min " . $seconds . "sec";
                    $users['quizzes_completed'] = count($cQuizzes);
                    $users['users'] = count($users['users']);
                    return response()->json($users, 200);
                } else {
                    return response()->json(array('error_type' => 'no data', 'code' => 404, 'errorr_message' => 'No data'), 404);
                }
            } else {
                return response()->json(array('error_type' => 'unauthorized', 'code' => 401, 'error_message' => 'Usnauthorized'), 401);
            }
        } else {
            return response()->json(array('error_type' => 'not_logged_in', 'code' => 401, 'errorr_message' => 'Not logged in'), 401);
        }
    }


    // POCETNA STRANA SVAKOG USERA 

    public function homeLearning()
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user) {
                $all = array();
                $completedSlides = array();
                $trainings = Training::where('company_id', $user->company_id)->where('published', 1)->get();
                if ($trainings) {
                    $countTrainings = count($trainings);
                    $completedTrainings = CompletedTraining::where('user_id', $user->id)->get();
                    if ($completedTrainings)
                        $countCompletedTrainings = count($completedTrainings);
                    $calculatingProcentTraining['training'] = round($countCompletedTrainings == 0 ? 0 :  $countCompletedTrainings / $countTrainings   * 100) . "%";
                    // array_push($all, $calculatingProcentTraining);
                    $quizzes = Quizzes::where('company_id', $user->company_id)->where('published', 1)->get();
                    if ($quizzes)
                        $countQuizzes = count($quizzes);
                    $completedQuizzes = CompletedQuiz::where('user_id', $user->id)->get();
                    if ($completedQuizzes)
                        $countCompletedQuizzes = count($completedQuizzes);
                    $calculatingProcentTraining['quizz'] = round($countCompletedQuizzes == 0 ? 0 : $countCompletedQuizzes / $countQuizzes * 100) . "%";
                    //array_push($all, $calculatingProcentQuiz);
                    $strategy = Strategy::where('company_id', $user->company_id)->first();
                    if ($strategy)
                        $strategySlides = Slides::where('strategy_id', $strategy->id)->get();
                    if ($strategySlides)
                        foreach ($strategySlides as $sd) {
                            $completedSlide = CompletedSlides::where('slides_id', $sd->id)->get()->toArray();
                            $completedSlides = array_merge($completedSlides, $completedSlide);
                        }
                    $calculatingProcentTraining['strategy'] = round(count($completedSlides) == 0 ? 0 : count($completedSlides) / count($strategySlides) * 100) . "%";
                    //   array_push($all, $calculatingProcentStrategy);

                    $calculatingProcentTraining['postignuca'] = round(($countCompletedTrainings + $countCompletedQuizzes) == 0 ? 0 : ($countCompletedTrainings + $countCompletedQuizzes) / ($countQuizzes + $countTrainings) * 100) . "%";
                    //    array_push($all, $postignuca);

                    $calculatingProcentTraining['cilj'] =  round(($countCompletedTrainings * 0.3) + ($countCompletedQuizzes * 0.5) + (count($completedSlides) * 0.2) / (($countQuizzes * 0.5) + ($countTrainings * 0.3) + (count($strategySlides) * 0.2)) * 100) . "%";
                    //     array_push($all, $cilj);

                    return response()->json($calculatingProcentTraining, 200);
                } else {
                    return response()->json(null, 200);
                }
            } else {
                return response()->json(array('error_type' => 'unauthorized', 'code' => 401, 'error_message' => 'Usnauthorized'), 401);
            }
        } else {
            return response()->json(array('error_type' => 'not_logged_in', 'code' => 401, 'errorr_message' => 'Not logged in'), 401);
        }
    }
}
