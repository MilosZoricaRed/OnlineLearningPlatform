<?php

namespace App\Http\Controllers;

use App\Training;
use App\Companys;
use App\Quizzes;
use App\Sector;
use App\TypeTraining;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $company = Companys::all();
        if ($company) {
            return response()->json($company, 200);
        } else {
            return response()->json(array('error_type' => 'no data', 'code' => 404, 'errorr_message' => 'No data'), 404);
        }
    }
    // SUPERADMIN GET 
    public function adminCompanys()
    {
        $company = Companys::all();
        if ($company) {
            return response()->json($company, 200);
        } else {
            return response()->json(array('error_type' => 'no data', 'code' => 404, 'errorr_message' => 'No data'), 404);
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
                $check = Companys::where('name', $request->name)->get();
                if ($check && count($check) == 0) {
                    $name = $request->name;
                    $email_extension = $request->email_extension;
                    $company = new Companys;
                    $company->name = $name;
                    $company->email_extension = $email_extension;
                    $company->save();
                    $sectors = $request->get('sectors');
                    if ($sectors) {
                        foreach ($sectors as $sector) {
                            $sector['name'];
                            $sector['company_id'] = $company->id;
                            $company->sectors()->insert($sector);
                        }
                    }
                    return response()->json(array($company, $sectors), 200);
                } else {
                    return response()->json("Company is already created!");
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
        $company = Training::where('company_id', $id)->with('company')->get();
        if ($company) {
            return response()->json($company, 200);
        } else {
            return response()->json(array('error_type' => 'no data', 'code' => 404, 'errorr_message' => 'No data'), 404);
        }
    }




    public function companysQuizzes($id = null)
    {
        if (Auth::check()) {
            $companys = Companys::find($id);
            if ($companys) {
                $companys['quizzes'] = Quizzes::where('company_id', $companys->id)->get();
                return response()->json($companys, 200);
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

    public function editSectors(Request $request, $company_id, $sectors_id)
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user) {
                $sectors = Sector::where('company_id', $company_id)->find($sectors_id);
                if ($request->name) $sectors->name = $request->name;
                $sectors->save();
                return response()->json($sectors, 200);
            } else {
                return response()->json(array('error_type' => 'unauthorized', 'code' => 404, 'errorr_message' => 'Unauthorized'), 404);
            }
        } else {
            return response()->json(array('error_type' => 'not_logged_in', 'code' => 401, 'errorr_message' => 'Not logged in'), 401);
        }
    }


    public function destroySector($id = null)
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user) {
                $sector = Sector::find($id);
                if ($sector->roles_id == 3) {
                    $sector->delete();
                } else {
                    return response()->json(array('error_type' => 'unauthorized', 'code' => 401, 'error_message' => 'Unauthorized'));
                }
                return response()->json($sector, 200);
            } else {
                return response()->json(array('error_type' => 'unauthorized', 'code' => 401, 'error_message' => 'Unauthorized'), 401);
            }
        } else {
            return response()->json(array('error_type' => 'not_logged_in', 'code' => 401, 'errorr_message' => 'Not loggedin'), 401);
        }
    }


    public function edit(Request $request, $id = null)
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user) {
                $company = Companys::find($id);
                if ($request->name) $company->name = $request->name;
                $company->save();
                return response()->json($company, 200);
            } else {
                return response()->json(array('error_type' => 'no data', 'code' => 404, 'errorr_message' => 'No data'), 404);
            }
        } else {
            return response()->json(array('error_type' => 'not_logged_in', 'code' => 401, 'errorr_message' => 'Not logged in'), 401);
        }
    }

    public function companysCategories($id = null)
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user) {
                $categories = Companys::with('categories')->find($id);
                if ($categories) {
                    return response()->json($categories, 200);
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
    public function AdminCompanysCategories($id = null)
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user) {
                $categories = Companys::with('categories')->find($id);
                if ($categories) {
                    return response()->json($categories, 200);
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



    public function companysCategoriesType($id = null)
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user) {
                $categories = Companys::with('categories')->find($id);
                if ($categories) {
                    return response()->json($categories, 200);
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

    public function companysSector($id = null)
    {
        $company = Companys::with('sectors')->find($id);
        if ($company) {
            return response()->json($company, 200);
        } else {
            return response()->json(array('error_type' => 'no data', 'code' => 404, 'errorr_message' => 'No data'), 404);
        }
    }

    // SUPERADMIN GETS
    public function adminCompanysSector($id = null)
    {
        $company = Companys::with('sectors')->find($id);
        if ($company) {
            return response()->json($company, 200);
        } else {
            return response()->json(array('error_type' => 'no data', 'code' => 404, 'errorr_message' => 'No data'), 404);
        }
    }

    public function sectors()
    {
        $user = Auth::user();
        if (Auth::check()) {
            $user = Auth::user();
            if ($user) {
                $sectors = Sector::where('company_id', $user->company_id)->get();
                if ($sectors) {
                    return response()->json($sectors, 200);
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
                $company = Companys::find($id);
                if ($user->roles_id == 3) {
                    $company->delete();
                }
                return response()->json(array('message' => 'Company successful deleted!', 'code' => 200, 'Company deleted' => $company), 200);
            } else {
                return response()->json(array('error_type' => 'no data', 'code' => 404, 'error_message' => 'No data to delete'), 404);
            }
        } else {
            return response()->json(array('error_type' => 'unauthorized', 'code' => 401, 'errorr_message' => 'Unauthorized'), 401);
        }
    }
}
