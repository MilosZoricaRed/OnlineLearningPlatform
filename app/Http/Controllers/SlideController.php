<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use DB;
use App\Slides;
use App\Classes\Utils;
use App\CompletedSlides;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Intro;
use App\Strategy;
use App\Training;

use Illuminate\Support\Facades\Input;

class SlideController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    // Training slides
    public function index($id = null)
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user) {
                $trainings = Training::find($id);
                if ($trainings->company_id == $user->company_id) {
                    $slides = Slides::where('training_id', $id)->with('training')->get();
                    if ($slides) {
                        foreach ($slides as $slide) {
                            $slide["status"] = "not complited";
                            if ($user->completedSlides()->find($slide->id)) {
                                $slide["status"] = "completed";
                            }
                        }
                        return response()->json($slides, 200);
                    } else {
                        return response()->json(array('error_type' => 'no data', 'code' => 404, 'errorr_message' => 'No data'), 404);
                    }
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


    // Intro slides
    public function introSlides($id = null)
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user) {
                $intro = Intro::find($id);
                if ($intro->company_id == $user->company_id) {
                    $slides = Slides::where('intro_id', $intro->id)->get();
                    if ($slides) {
                        $intro['slides'] = $slides;
                        return response()->json($intro, 200);
                    } else {
                        return response()->json(null, 200);
                    }
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
    public function adminIntroSlides($company_id = null)
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user) {
                $intro = Intro::where('company_id', $company_id)->first();
                $slides = Slides::where('intro_id', $intro->id)->get();
                if ($slides) {
                    $intro['slides'] = $slides;
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

    // Strategy slides
    public function strategySlides($id = null)
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user) {
                $strategy = Strategy::find($id);
                if ($strategy->company_id == $user->company_id) {
                    $slides = Slides::where('strategy_id', $strategy->id)->get();
                    if ($slides) {
                        $strategy['slides'] = $slides;
                        return response()->json($strategy, 200);
                    } else {
                        return response()->json(array('error_type' => 'no data', 'code' => 404, 'errorr_message' => 'no data'), 404);
                    }
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
    public function adminStrategySlides($company_id = null)
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user) {
                $strategy = Strategy::where('company_id', $company_id)->first();
                if ($strategy) {
                    $slides = Slides::where('strategy_id', $strategy->id)->get();
                } else {
                    return response()->json(null, 200);
                }
                if ($slides) {
                    $strategy['slides'] = $slides;
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


    public function completed(Request $request, $id = null)
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user) {
                $check = CompletedSlides::where('slides_id', $id)->where('user_id', $user->id)->get();
                if ($check && count($check) == 0) {
                    $completed = new CompletedSlides;
                    $completed->user_id = $user->id;
                    $completed->slides_id = $id;
                    $completed->save();
                } else {
                    return response()->json(null, 200);
                }
                return response()->json("message: success", 200);
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
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeTrainingSlides(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user) {
                $slides = $request->get('content');
                if ($slides) {
                    foreach ($slides as $slide) {
                        $slide['title'];
                        $slide['tekst'];
                        $slide['photo_src'];
                        $slide['video_src'];
                        $slide['audio_src'];
                        $slide['training_id'];
                        $slide['audio_duration'];
                    }
                    DB::table('slides')->insert($slides);
                    return response()->json($slides, 200);
                } else {
                    return response()->json("That training does not exists!", 401);
                }
            } else {
                return response()->json(array('error_type' => 'unauthorized', 'code' => 401, 'errorr_message' => 'Unauthorized'), 401);
            }
        } else {
            return response()->json(array('error_type' => 'not_logged_in', 'code' => 401, 'errorr_message' => 'not_logged_in'), 401);
        }
    }


    public function storeStrategySlides(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user) {
                $slides = $request->get('content');
                if ($slides) {
                    foreach ($slides as $slide) {
                        $slide['title'];
                        $slide['tekst'];
                        $slide['photo_src'];
                        $slide['video_src'];
                        $slide['audio_src'];
                        $slide['audio_duration'];
                        $slide['strategy_id'];
                    }
                    DB::table('slides')->insert($slides);
                    return response()->json($slides, 200);
                } else {
                    return response()->json("That training does not exists!", 401);
                }
            } else {
                return response()->json(array('error_type' => 'unauthorized', 'code' => 401, 'errorr_message' => 'Unauthorized'), 401);
            }
        } else {
            return response()->json(array('error_type' => 'not_logged_in', 'code' => 401, 'errorr_message' => 'Not logged in'), 401);
        }
    }


    public function storeIntroSlides(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user) {
                $slides = $request->get('content');
                if ($slides) {
                    foreach ($slides as $slide) {
                        $slide['title'];
                        $slide['tekst'];
                        $slide['photo_src'];
                        $slide['video_src'];
                        $slide['audio_src'];
                        $slide['audio_duration'];
                        $slide['intro_id'];
                    }
                    DB::table('slides')->insert($slides);
                    return response()->json($slides, 200);
                } else {
                    return response()->json("That training does not exists!", 401);
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
                $slide = Slides::with('training')->findOrFail($id);
                if ($slide) {
                    return response()->json($slide, 200);
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
                $data = Input::all();
                $slide = Slides::find($id);
                if (array_key_exists('title', $data)) $slide->title = $request->title;
                if (array_key_exists('tekst', $data)) $slide->tekst = $request->tekst;
                if (array_key_exists('photo_src', $data)) $slide->photo_src = $request->photo_src;
                if (array_key_exists('video_src', $data)) $slide->video_src = $request->video_src;
                if (array_key_exists('audio_src', $data)) $slide->audio_src = $request->audio_src;
                if (array_key_exists('audio_duration', $data)) $slide->audio_duration = $request->audio_duration;
                $slide->save();
                return response()->json($slide, 200);
            } else {
                return response()->json(array('error_type' => 'unauthorized', 'code' => 401, 'errorr_message' => 'Unauthorized'), 401);
            }
        } else {
            return response()->json(array('error_type' => 'not_logged_in', 'code' => 401, 'errorr_message' => 'Not logged in'), 401);
        }
    }

    public function showSlideImage(Request $request, $media)
    {
        if (File::exists(app_path("slide_images/$media"))) {
            return response()->file(app_path("slide_images/$media"), [
                'Content-Type' => 'file',
                'Content-Disposition' => 'inline; filename="image/jpg"'
            ]);
        } else {
            return response()->json(array('error_type' => 'missing_inputs', 'code' => 422, 'error_message' => 'No file.'), 422);
        }
    }

    public function showSlideVideo(Request $request, $media)
    {
        if (File::exists(app_path("slide_videos/$media"))) {
            return response()->file(app_path("slide_videos/$media"), [
                'Content-Type' => 'file',
                'Content-Disposition' => 'inline; filename="video/mp3"'
            ]);
        } else {
            return response()->json(array('error_type' => 'missing_inputs', 'code' => 422, 'error_message' => 'No file.'), 422);
        }
    }

    public function showSlideAudio(Request $request, $media)
    {
        if (File::exists(app_path("slide_audio/$media"))) {
            return response()->file(app_path("slide_audio/$media"), [
                'Content-Type' => 'file',
                'Content-Disposition' => 'inline; filename="audio/mpg4"'
            ]);
        } else {
            return response()->json(array('error_type' => 'missing_inputs', 'code' => 422, 'error_message' => 'No file.'), 422);
        }
    }

    public function saveSlideImage(Request $request)
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
            $filenameNew = 'slideImage' . $random_name . '.' . $extension;
            $moveImg = $inputFile->move(app_path() . '/slide_images/', $filenameNew);
            if ($moveImg) {
                $url = Utils::$MAINURL_address . '/slideimage/' . $filenameNew;
                if (File::exists(app_path("slide_images/" . $filenameNew))) {
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

    public function saveSlideVideo(Request $request)
    {
        $inputFile = $request->file('file');
        if ($inputFile) {
            $random_name = str_random(8);
            $extension = strtolower($inputFile->getClientOriginalExtension());
            /* if (!$extension) {
                return response()->json(array('error_type' => 'missing_inputs', 'code' => 422, 'error_message' => 'No file.'), 422);
            } else if ($extension != 'mp4') {
                return response()->json(array('error_type' => 'missing_inputs', 'code' => 422, 'error_message' => 'No file.'), 422);
            } */
            $filenameNew = 'slideVideo_' . $random_name . '.' . $extension;
            $moveImg = $inputFile->move(app_path() . '/slide_videos/', $filenameNew);
            if ($moveImg) {
                $url = Utils::$MAINURL_address . '/slidevideo/' . $filenameNew;
                if (File::exists(app_path("slide_videos/" . $filenameNew))) {
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

    public function saveSlideAudio(Request $request)
    {
        $inputFile = $request->file('file');
        if ($inputFile) {
            $random_name = str_random(8);
            $extension = strtolower($inputFile->getClientOriginalExtension());
            /* if (!$extension) {
                return response()->json(array('error_type' => 'missing_inputs', 'code' => 422, 'error_message' => 'No file.'), 422);
            } else if ($extension != 'mp3') {
                return response()->json(array('error_type' => 'missing_inputs', 'code' => 422, 'error_message' => 'No file.'), 422);
            } */
            $filenameNew = 'slideAudio_' . $random_name . '.' . $extension;
            $moveImg = $inputFile->move(app_path() . '/slide_audio/', $filenameNew);
            if ($moveImg) {
                $url = Utils::$MAINURL_address . '/slideaudio/' . $filenameNew;
                if (File::exists(app_path("slide_audio/" . $filenameNew))) {
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
                $slide = Slides::find($id);
                $slide->delete();
                return response()->json('Slide deleted!', 200);
            } else {
                return response()->json(array('error_type' => 'unauthorized', 'code' => 401, 'errorr_message' => 'Unauthorized'), 401);
            }
        } else {
            return response()->json(array('error_type' => 'not_logged_in', 'code' => 401, 'errorr_message' => 'Not logged in'), 401);
        }
    }
}
