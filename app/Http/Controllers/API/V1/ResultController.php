<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\Result\ResultCollection;
use App\Http\Resources\V1\Result\ResultResource;
use Illuminate\Support\Facades\DB;
use App\Models\Result;
use App\Models\Semester;
use App\Helpers\Helper;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ResultController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:sanctum', ['only' => ['store', 'update', 'destroy', 'cordinating_module', 'lecturers_results']]);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $results = Result::where('semester_id', Helper::semester())->orderBy('id', 'DESC')->with(['module.cordinator', 'module.module_bank', 'module.level']);
        return new ResultCollection($results->get());
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
     * @param  \App\Models\Result  $result
     * @return \Illuminate\Http\Response
     */
    public function show(Result $result)
    {
        return (new ResultResource($result->loadMissing(['module.cordinator', 'module.module_bank', 'assessments.student'])))
            ->response()
            ->setStatusCode(200);
    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Result  $result
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Result $result)
    {
         $this->validate($request, [
            'assessments.*.score' => 'required|numeric|between:0,100',
        ]);

        try{
            DB::beginTransaction();

            $assessments = $result->assessments;
            foreach ($assessments as $key => $assessment) {
                $score = $request->assessments[$key]['score'];
                $assessment->score = $score;
                $assessment->remarks = Helper::remarks($score);
                $assessment->save();
            }
            $result->status = $request->status;
            $result->save();

            DB::commit();
            return response()->json(['status' => 'success'])
                ->setStatusCode(201);

        }catch(\Exception $e){
             DB::rollBack();
            \Log::error($e->getMessage());
            return response()->json([
                'error'=>$e->getMessage(),
                'message'=>'An error occured while updating a results!!'
            ])->setStatusCode(500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Result  $result
     * @return \Illuminate\Http\Response
     */
    public function destroy(Result $result)
    {
        //
    }


    /**
     * Cordinating Module the specified resource from storage.
     *
     * @param  \App\Models\Module  $module
     * @return \Illuminate\Http\Response
     */
    public function cordinating_module()
    {
        $lecturer_id = auth()->user()->lecturer->id;
        $results = Result::where('semester_id', Helper::semester())->where('cordinator_id', $lecturer_id)->orderBy('id', 'DESC')->with(['module.module_bank', 'module.level']);
        return new ResultCollection($results->get());
    }

    /**
     * Cordinating Module the specified resource from storage.
     *
     * @param  \App\Models\Module  $module
     * @return \Illuminate\Http\Response
     */
    public function promotion_check()
    {
        $semester = Semester::orderBy('id','DESC')->first();
        $semester_id = null;
        if($semester){
            $semester_id = $semester->id;
        }
        $results = Result::where('semester_id', $semester_id)->pluck('status')->toArray();
        $data = "unset";
        if(!in_array('save',$results) && !in_array('submit',$results)){
            $data = "set";
        }
        return response()->json([
            'data' => [
                'check' => $data,
                'semester'=>$semester,
            ],
        ])->setStatusCode(200);
    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Result  $result
     * @return \Illuminate\Http\Response
     */
    public function update_status(Result $result)
    {

        try{
            DB::beginTransaction();

            if($result->status === 'publish'){
                $result->status = 'save';
            }else{
                $result->status = 'publish';

            }
            $result->save();

             DB::commit();
            return response()->json(['status' => 'success'])
                ->setStatusCode(201);

        }catch(\Exception $e){
             DB::rollBack();
            \Log::error($e->getMessage());
            return response()->json([
                'error'=>$e->getMessage(),
                'message'=>'An error occured while updating results status!!'
            ])->setStatusCode(500);
        }
    }


    public function lecturers_results()
    {
        $lecturerModules = auth()->user()->lecturer->modules->pluck('id')->toArray();
        $results = Result::whereIn('module_id', $lecturerModules)->orderBy('id', 'DESC')->with(['module.cordinator', 'module.module_bank'])->get();
        return new ResultCollection($results);
    }



    // public function results()
    // {
    //     $student = auth()->user()->student->id;
    //     return response()->json(['data'=>$student->with('results')])->setStatusCode(200);
    // }

}