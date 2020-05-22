<?php

namespace App\Http\Controllers;

use App\Slides;
use App\Likes;
use App\Quizzes;
use App\Training;
use App\Category;
use App\TypeTraining;
use DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CategoryController extends Controller
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
                $categorys = Category::where('company_id', $user->company_id)->get();
                if ($categorys) {
                    return response()->json($categorys, 200);
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

    public function categorysQuizzes($id = null)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $localCounter = 0;
            $categorys = Category::where('company_id', $user->company_id)->with('typeQuizz')->find($id);
            if ($categorys) {
                $getQuizzes = null;
                foreach ($categorys['typeQuizz'] as $training) {
                    if ($getQuizzes == null) $getQuizzes = Quizzes::where('type_id', $training->id)->where('published', '1');
                    else $getQuizzes = $getQuizzes->orWhere('type_id', $training->id)->where('published', '1');
                }
                //return response()->json();
                $quizzCollection = $getQuizzes->with('quizzType')->get();
                if ($quizzCollection) {
                    foreach ($quizzCollection as $quizz) {
                        $check = Training::where('id', $quizz->training_id)->first();
                        if ($check)
                            if ($user->completedQuiz()->find($quizz->id)) {
                                $quizz["status"] = "completed";
                            } else if ($user->completedTraining()->find($quizz->training_id)) {
                                $quizz["status"] = "available";
                            } else {
                                $quizz["status"] = "not available";
                            }
                    }
                } else {
                    return response()->json(null, 200);
                }
                $categorys["quizzes"] = $quizzCollection;
                return response()->json($categorys, 200);
            } else {
                return response()->json(array('error_type' => 'no data', 'code' => 404, 'errorr_message' => 'No data'), 404);
            }
        } else {
            return response()->json(array('error_type' => 'not_logged in', 'code' => 401, 'errorr_message' => 'Not logged in'), 401);
        }
    }



    // SUPERADMIN GETS
    public function adminCategorysQuizzes($company_id = null, $category_id = null)
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user) {
                $localCounter = 0;
                $categorys = Category::where('company_id', $company_id)->with('typeQuizz')->find($category_id);
                if ($categorys) {
                    $getQuizzes = null;
                    foreach ($categorys['typeQuizz'] as $training) {
                        if ($getQuizzes == null) $getQuizzes = Quizzes::where('type_id', $training->id);
                        else $getQuizzes = $getQuizzes->orWhere('type_id', $training->id);
                    }
                    $quizzCollection = $getQuizzes->with('quizzType')->get();
                    if ($quizzCollection) {
                        foreach ($quizzCollection as $quizz) {
                            $check = Training::where('id', $quizz->training_id)->first();
                            if ($check)
                                if ($user->completedQuiz()->find($quizz->id)) {
                                    $quizz["status"] = "completed";
                                } else if ($user->completedTraining()->find($quizz->id)) {
                                    $quizz["status"] = "available";
                                } else {
                                    $quizz["status"] = "not available";
                                }
                        }
                    } else {
                        return response()->json(null, 200);
                    }

                    $categorys["quizzes"] = $quizzCollection;
                    return response()->json($categorys, 200);
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




    public function categoryTrainings($id = null)
    {
        $user = Auth::user();
        $categorys = Category::where('company_id', $user->company_id)->with('typeTraining')->find($id);
        if ($categorys) {
            $trainings = array();
            foreach ($categorys['typeTraining'] as $type) {
                $training = Training::where('type_id', $type->id)->get()->toArray();
                if ($training) {
                    $trainings = array_merge($trainings, $training);
                } else {
                    return response()->json("No trainings!", 404);
                }
            }
            return response()->json($trainings);
        } else {
            return response()->json(array('error_type' => 'no data', 'code' => 404, 'errorr_message' => 'No data'), 404);
        }
    }



    // SUPERADMIN GETS

    public function superadminCategoryTrainings($company_id = null, $id = null)
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user) {
                $localCounter = 0;
                $categorys = Category::where('company_id', $company_id)->with('typeTraining')->find($id);
                if ($categorys) {
                    $getTrainings = null;
                    foreach ($categorys['typeTraining'] as $type) {
                        if ($getTrainings == null) $getTrainings = Training::where('type_id', $type->id);
                        else $getTrainings = $getTrainings->orWhere('type_id', $type->id);
                    }
                    $trainingCollection = $getTrainings->with('trainingType', 'trainingBullets')->get();
                    if ($trainingCollection) {
                        foreach ($trainingCollection as $training) {
                            $check = Training::where('id', $training->id)->first();
                            if ($check)
                                $training["status"] = "not available";
                            if ($user->completedTraining()->find($training->id)) {
                                $training["status"] = "completed";
                            } else {
                                if ($localCounter < 2) {
                                    $training["status"] = "available";
                                    $localCounter++;
                                }
                            }
                            $training['has_text'] = true;
                            $training['has_video'] = false;
                            $training['has_photo'] = false;
                            $slides = Slides::where('training_id', $training->training_id)->get();
                            if ($slides) {
                                foreach ($slides as $slide) {
                                    if ($slide['video_src'] && $slide['video_src'] != '') {
                                        $training['has_video'] = true;
                                    }
                                    if ($slide['photo_src'] && $slide['photo_src'] != '') {
                                        $training['has_photo'] = true;
                                    }
                                }
                            } else {
                                return response()->json(null, 200);
                            }
                            $training['likes'] = null;
                            $likes = Likes::where('training_id', $training->id)->get();
                            if ($likes) {
                                $training['likes'] = $likes->count();
                            } else {
                                return response()->json(null, 200);
                            }
                            $training['myLike'] = false;
                            $ilike = Likes::where('training_id', $training->id)->where('user_id', $user->id)->first();
                            if ($ilike && $ilike->count() > 0) {
                                $training['myLike'] = true;
                            } else $training['myLike'] = false;
                        }
                    } else {
                        return response()->json(null, 200);
                    }
                    $categorys["trainings"] = $trainingCollection;
                    return response()->json($categorys, 200);
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
                $check = Category::where('name', $request->name)->get();
                if ($check && count($check) == 0) {
                    $name = $request->name;
                    $company_id = $request->company_id;
                    $name_type = $request->name_type;
                    $description = $request->description;
                    $category = new Category;
                    $category->name = $name;
                    $category->company_id = $company_id;
                    $category->description = $description;
                    $category->save();
                    foreach ($name_type as $type) {
                        $type["category_id"] = $category->id;
                        $category->typeTraining()->insert($type);
                    }
                    return response()->json(array($category, $name_type), 200);
                } else {
                    return response()->json("Category is already created!");
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
            $categorys = TypeTraining::where('category_id', $id)->with('category')->get();
            if ($categorys) {
                return response()->json($categorys, 200);
            } else {
                return response()->json(array('error_type' => 'no data', 'code' => 404, 'errorr_message' => 'No data'), 404);
            }
        } else {
            return response()->json(array('error_type' => 'not_logged_in', 'code' => 401, 'errorr_message' => 'Not logged in'), 401);
        }
    }

    // SUPERADMIN GETS
    public function adminCategoriesTypes($company_id = null, $id = null)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $categorys = Category::where('company_id', $company_id)->find($id);
            if ($categorys) {
                $types = TypeTraining::where('category_id', $categorys->id)->with('category')->get();
                if ($types) {
                    return response()->json($types);
                } else {
                    return response()->json(array('error_type' => 'no data', 'code' => 404, 'errorr_message' => 'No data'), 404);
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
    public function edit(Request $request, $id)
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user) {
                $category = Category::find($id);
                if ($request->name) $category->name = $request->name;
                if ($request->description) $category->description = $request->description;
                $category->save();
                $oldTypes = TypeTraining::where('category_id', $id)->get();
                if ($oldTypes) {
                    foreach ($oldTypes as $old) {
                        $old->delete();
                    }
                }
                $typeTraining = $request->get('types');
                if ($typeTraining) {
                    foreach ($typeTraining as $type) {
                        $type['category_id'] = $id;
                        $type['name_type'];
                        $category->category()->insert($type);
                    }
                    return response()->json("Category updated", 200);
                }
                return response()->json($category, 200);
            } else {
                return response()->json(array('error_type' => 'unauthorized', 'code' => 404, 'errorr_message' => 'Unauthorized'), 404);
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user) {
                $category = Category::find($id);
                if ($user->roles_id == 3) {
                    $category->delete();
                }
                return response()->json(array('message' => 'Category successful deleted!', 'code' => 200, 'Category deleted' => $category), 200);
            } else {
                return response()->json(array('error_type' => 'no data', 'code' => 404, 'error_message' => 'No data to delete'), 404);
            }
        } else {
            return response()->json(array('error_type' => 'unauthorized', 'code' => 401, 'errorr_message' => 'Unauthorized'), 401);
        }
    }
}
