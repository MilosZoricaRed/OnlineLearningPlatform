<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\CompletedTraining;
use App\Likes;
use App\Slides;
use App\Training;
use App\Bullet;
use App\Classes\Utils;
use Illuminate\Support\Facades\File;
use App\Messages;
use App\TypeTraining;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Category;
use App\Question;
use App\Quizzes;
use DB;

class TrainingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id = null)
    {
        if (Auth::user()) {
            $user = Auth::user();
            if ($user) {
                $trainings = Training::where('company_id', $user->company_id)->where('published', '1')->with('company', 'trainingBullets')->get();
                if ($trainings) {
                    $localCounter = 0;
                    foreach ($trainings as $training) {
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
                        $slides = Slides::where('training_id', $training->id)->get();
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
                        }
                        $training['myLike'] = false;
                        $ilike = Likes::where('training_id', $training->id)->where('user_id', $user->id)->first();
                        if ($ilike && $ilike->count() > 0) {
                            $training['myLike'] = true;
                        } else $training['myLike'] = false;
                    }
                    return response()->json($trainings, 200);
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

    public function adminTrainings($id = null)
    {
        if (Auth::user()) {
            $user = Auth::user();
            if ($user) {
                $trainings = Training::where('company_id', $id)->with('company', 'trainingBullets')->get();
                if ($trainings) {
                    $localCounter = 0;
                    foreach ($trainings as $training) {
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
                        $slides = Slides::where('training_id', $training->id)->get();
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
                        }
                        $training['myLike'] = false;
                        $ilike = Likes::where('training_id', $training->id)->where('user_id', $user->id)->first();
                        if ($ilike && $ilike->count() > 0) {
                            $training['myLike'] = true;
                        } else $training['myLike'] = false;
                    }
                    return response()->json($trainings, 200);
                } else {
                    return response()->json(array('error_type' => 'no data', 'code' => 404, 'errorr_message' => 'No data'), 404);
                }
            } else {
                return response()->json(array('error_type' => 'unauthorized', 'code' => 401, 'errorr_message' => 'Unauthorized'), 401);
            }
        } else {
            return response()->json(array('error_type' => 'not_logged_in', 'code' => 401, 'errorr_message' => 'not_logged_in'), 401);
        }
    }


    public function showDefaults()
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user) {
                $training = Training::where('company_id', $user->company_id)->where('published', '1')->where('default_id', 0)->get();
                if ($training) {
                    return response()->json($training, 200);
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

    public function showCustomDefault(Request $request, $id)
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user) {
                $check = Training::where('id', $id)->first();
                $checkNew = new Training;
                $checkNew->default_id = $check->id;
                $checkNew->type_id = $check->type_id;
                $checkNew->name = $check->name;
                $checkNew->tekst = $check->tekst;
                $checkNew->duration = $check->duration;
                $checkNew->company_id = $request->company_id;
                // $check->id = $id;
                $checkNew->save();
                return response()->json($checkNew, 200);
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


    public function store(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user) {
                $check = Training::where('name', $request->name)->get();
                if ($check && count($check) == 0) {
                    $type_id = $request->type_id;
                    $name = $request->name;
                    $tekst = $request->tekst;
                    $duration = $request->duration;
                    $default_id = $request->default_id;
                    $company_id = $request->company_id;
                    $detail_photo = $request->detail_photo;
                    $list_photo = $request->list_photo;
                    $tekst_bullet = $request->tekst_bullet;
                    //$bullet_records = array();
                    $training = new Training;
                    $training->type_id = $type_id;
                    $training->name = $name;
                    $training->tekst = $tekst;
                    $training->duration = $duration;
                    $training->default_id = $default_id;
                    $training->company_id = $company_id;
                    $training->list_photo = $list_photo;
                    $training->detail_photo = $detail_photo;
                    $training->save();
                    foreach ($tekst_bullet as $bullet) {
                        $bullet["training_id"] = $training->id;
                        $training->trainingBullets()->insert($bullet);
                    }
                    //$training->trainingBullets()->insert($bullet_records);
                    return response()->json(array($training, $tekst_bullet), 200);
                } else {
                    return response()->json('Training with that name already exists!', 401);
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
    public function completed(Request $request, $id = null)
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user) {
                $check = CompletedTraining::where('training_id', $id)->where('user_id', $user->id)->get();
                if ($check && count($check) == 0) {
                    $completed = new CompletedTraining;
                    $completed->user_id = $user->id;
                    $completed->training_id = $id;
                    $completed->save();

                    $training = Training::find($id);
                    if ($training) {
                        $type = TypeTraining::find($training->type_id);
                        $trainings = $this->categorysTraining($type->category_id);
                        $cnt = count($trainings->original->trainings);
                        $cntCompleted = 0;
                        foreach ($trainings->original->trainings as $training) {
                            if ($training['status'] == "completed") {
                                $cntCompleted++;
                            }
                        }
                        if ($cntCompleted < $cnt - 1) {
                            $newMessage = new Messages;
                            $newMessage->tekst = "Novi trening je dostupan" . $cntCompleted;
                            $newMessage->user_id = $user->id;
                            $newMessage->active = 1;
                            $newMessage->save();
                        }
                    } else {
                        return response()->json(null, 200);
                    }
                    return response()->json("created: success", 200);
                } else {
                    return response()->json("This training is already completed!");
                }
            } else {
                return response()->json(array('error_type' => 'unauthorized', 'code' => 401, 'errorr_message' => 'Unauthorized'), 401);
            }
        } else {
            return response()->json(array('error_type' => 'not_logged_in', 'code' => 401, 'errorr_message' => 'Not logged in'), 401);
        }
    }



    public function categorysTraining($id = null)
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user) {
                $localCounter = 0;
                $categorys = Category::where('company_id', $user->company_id)->with('typeTraining')->find($id);
                if ($categorys) {
                    $getTrainings = null;
                    foreach ($categorys['typeTraining'] as $type) {
                        if ($getTrainings == null) $getTrainings = Training::where('type_id', $type->id)->where('published', '1');
                        else $getTrainings = $getTrainings->orWhere('type_id', $type->id)->where('published', '1');
                    }
                    $trainingCollection = $getTrainings->with('trainingType', 'trainingBullets')->get();
                    if ($trainingCollection) {
                        foreach ($trainingCollection as $training) {
                            $check = Training::where('id', $training->id)->where('published', '1')->first();
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


    // SUPERADMIN GETS

    public function superadminTrainings($company_id = null, $id = null)
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user) {
                $training = Training::where('company_id', $company_id)->with('trainingType', 'trainingBullets')->find($id);
                if ($training) {
                    $check = Training::where('id', $id)->first();
                    $training["status"] = "not available";
                    if ($check)
                        $training["status"] = "available";
                    $user = Auth::user();
                    if ($user->completedTraining()->find($training->id)) {
                        $training["status"] = "completed";
                    }
                    $quizzes = Quizzes::where('training_id', $id)->get();
                    if ($quizzes) {
                        foreach ($quizzes as $quz) {
                            $training['quizz_id'] = $quz['id'];
                        }
                    } else {
                        return response()->json(null, 200);
                    }
                    $training['has_text'] = true;
                    $training['has_video'] = false;
                    $training['has_photo'] = false;
                    $slides = Slides::where('training_id', $training->id)->get();
                    if ($slides) {
                        foreach ($slides as $slide) {
                            if ($slide['video_src'] && $slide['video_src'] != '') {
                                $training['has_video'] = true;
                            }
                            if ($slide['photo_src'] && $slide['photo_src'] != '') {
                                $training['has_photo'] = true;
                            }
                        }
                    }
                    $training['likes'] = null;
                    $likes = Likes::where('training_id', $training->id)->get();
                    if ($likes) {
                        $training['likes'] = $likes->count();
                    }
                    $training['myLike'] = false;
                    $ilike = Likes::where('training_id', $training->id)->where('user_id', $user->id)->first();
                    if ($ilike && $ilike->count() > 0) {
                        $training['myLike'] = true;
                    } else $training['myLike'] = false;
                    return response()->json($training, 200);
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
                $training = Training::where('company_id', $user->company_id)->where('published', '1')->with('trainingType', 'trainingBullets')->find($id);
                if ($training) {
                    $check = Training::where('id', $id)->first();
                    $training["status"] = "not available";
                    if ($check)
                        $training["status"] = "available";
                    $user = Auth::user();
                    if ($user->completedTraining()->find($training->id)) {
                        $training["status"] = "completed";
                    }
                    $training['has_text'] = true;
                    $training['has_video'] = false;
                    $training['has_photo'] = false;
                    $slides = Slides::where('training_id', $training->id)->get();
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
                    }
                    $training['myLike'] = false;
                    $ilike = Likes::where('training_id', $training->id)->where('user_id', $user->id)->first();
                    if ($ilike && $ilike->count() > 0) {
                        $training['myLike'] = true;
                    } else $training['myLike'] = false;
                    return response()->json($training, 200);
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




    // training/{id}/slides
    public function trainingSlides($id = null)
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user) {
                $trainings = Training::where('company_id', $user->company_id)->where('published', '1')->find($id);
                if ($trainings) {
                    $check = Training::where('id', $id)->where('published', '1')->first();
                    if ($check)
                        $slides = Slides::where('training_id', $trainings->id)->get();
                    if ($slides) {
                        $trainings["slides"] = $slides;
                    }
                    return response()->json($trainings, 200);
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
    public function adminTrainingSlides($company_id = null, $training_id = null)
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user) {
                $trainings = Training::where('company_id', $company_id)->find($training_id);
                if ($trainings) {
                    $check = Training::where('id', $training_id)->first();
                    if ($check) {
                        $slides = Slides::where('training_id', $trainings->id)->get();
                    } else {
                        return response()->json(null, 200);
                    }
                    if ($slides) {
                        $trainings["slides"] = $slides;
                    } else {
                        return response()->json(null, 200);
                    }
                    return response()->json($trainings, 200);
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


    public function showTrainingImagesList(Request $request, $media)
    {
        if (File::exists(app_path("training_images_list/$media"))) {
            return response()->file(app_path("training_images_list/$media"), [
                'Content-Type' => 'file',
                'Content-Disposition' => 'inline; filename="image/jpg"'
            ]);
        } else {
            return response()->json(array('error_type' => 'missing_inputs', 'code' => 422, 'error_message' => 'No file.'), 422);
        }
    }



    public function saveTrainingImageList(Request $request)
    {
        $inputFile = $request->file('file');
        if ($inputFile) {
            $random_name = str_random(8);
            $extension = strtolower($inputFile->getClientOriginalExtension());
            /* if (!$extension) {
                return response()->json(array('error_type' => 'missing_inputs', 'code' => 422, 'error_message' => 'No file.'), 422);
            } else if ($extension != 'jpg') {
                return response()->json(array('error_type' => 'missing_inputs', 'code' => 422, 'error_message' => 'No file.'), 422);
            } */
            $filenameNew = 'trainingImageList' . $random_name . '.' . $extension;
            $moveImg = $inputFile->move(app_path() . '/training_images_list/', $filenameNew);
            if ($moveImg) {
                $url = Utils::$MAINURL_address . '/trainingimagelist/' . $filenameNew;
                if (File::exists(app_path("training_images_list/" . $filenameNew))) {
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



    public function showTrainingImagesDetail(Request $request, $media)
    {
        if (File::exists(app_path("training_images_detail/$media"))) {
            return response()->file(app_path("training_images_detail/$media"), [
                'Content-Type' => 'file',
                'Content-Disposition' => 'inline; filename="image/jpg"'
            ]);
        } else {
            return response()->json(array('error_type' => 'missing_inputs', 'code' => 422, 'error_message' => 'No file.'), 422);
        }
    }


    public function saveTrainingImageDetail(Request $request)
    {
        $inputFile = $request->file('file');
        if ($inputFile) {
            $random_name = str_random(8);
            $extension = strtolower($inputFile->getClientOriginalExtension());
            /* if (!$extension) {
                return response()->json(array('error_type' => 'missing_inputs', 'code' => 422, 'error_message' => 'No file.'), 422);
            } else if ($extension != 'jpg') {
                return response()->json(array('error_type' => 'missing_inputs', 'code' => 422, 'error_message' => 'No file.'), 422);
            } */
            $filenameNew = 'trainingImageDetail' . $random_name . '.' . $extension;
            $moveImg = $inputFile->move(app_path() . '/training_images_detail/', $filenameNew);
            if ($moveImg) {
                $url = Utils::$MAINURL_address . '/trainingimagedetail/' . $filenameNew;
                if (File::exists(app_path("training_images_detail/" . $filenameNew))) {
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
                $training = Training::find($id);
                if ($request->type_id) $training->type_id = $request->type_id;
                if ($request->tekst) $training->tekst = $request->tekst;
                if ($request->name) $training->name = $request->name;
                if ($request->default_id) $training->default_id = $request->default_id;
                if ($request->company_id) $training->company_id = $request->company_id;
                if ($request->photo_src) $training->photo_src = $request->photo_src;
                $training->save();
                $oldBullets = Bullet::where('training_id', $id)->get();
                if ($oldBullets) {
                    foreach ($oldBullets as $old) {
                        $old->delete();
                    }
                }
                $bullets = $request->get('bullets');
                if ($bullets) {
                    foreach ($bullets as $bullet) {
                        $bullet['training_id'] = $id;
                        $bullet['tekst_bullet'];
                        $training->trainingBullets()->insert($bullet);
                    }
                    return response()->json($training, 200);
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

    // SUPERADMIN published
    public function changePublished(Request $request, $id = null)
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user) {
                $training = Training::find($id);
                if ($request->published) $training->published = $request->published;
                $training->save();
                return response()->json("message: success");
            } else {
                return response()->json('error', 401);
            }
        } else {
            return response()->json('error', 401);
        }
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
                $training = Training::find($id);
                if ($training) {
                    $training->delete();
                    return response()->json(array('message' => 'Training successful deleted!', 'code' => 200, 'Training deleted' => $training), 200);
                } else {
                    return response()->json(array('error_type' => 'no data', 'code' => 404, 'error_message' => 'No data to delete'), 404);
                }
            } else {
                return response()->json(array('error_type' => 'unauthorized', 'code' => 401, 'errorr_message' => 'Unauthorized'), 401);
            }
        } else {
            return response()->json(array('error_type' => 'not_logged_in', 'code' => 401, 'errorr_message' => 'Not logged in'), 401);
        }
    }
}
