<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\AssessmentsMarksModel;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;


class AssessmentGradesController extends Controller
{
    //
    public function getAssessmentsMarks(Request $req)
    {

        
        $marks = DB::table('assessment_marks')
        ->join('assessments','assessments.asId','=','assessment_marks.assessment_id')
        ->join('users','users.userId','=','assessment_marks.student_id')
        // ->where('assessment_marks.assessment_id', '=', $req->input('assessmentId'))
            ->select('users.*','assessments.*','assessment_marks.*')
            ->get();

        return $marks;
    // }
    }
    public function getEveryInfoWithMarks(){
        $marks = DB::table('lecturesoutline')
        ->join('assessments','assessments.outlineId','=','lecturesoutline.outlineId')
        ->join('assessment_marks','assessments.asId','=','assessment_marks.assessment_id')
        ->join('users','users.userId','=','assessment_marks.student_id')
        // ->where('assessment_marks.assessment_id', '=', $req->input('assessmentId'))
            ->select('users.*','assessments.*','assessment_marks.*','lecturesoutline.*')
            ->get();
        return $marks;

    }
    //
    public function addAssessmentMarks(Request $request)
    {

        $added_by =  auth()->user()->userId;
        $marks = $request->input('students');
 
        foreach ($marks as $eachMarks) {


            $addAssessment = new AssessmentsMarksModel();
            $addAssessment->assessment_id = $eachMarks['assessment_id'];
            $addAssessment->qno = $eachMarks['qno'];
            $addAssessment->obtmarks =$eachMarks['obtmarks'] ;
            $addAssessment->total_marks = $eachMarks['total_marks'] ;
            $addAssessment->student_id =$eachMarks['student_id'];
            $addAssessment->objName =$eachMarks['objName'];
            $addAssessment->added_by = $added_by;
            $addAssessment->created_at = $eachMarks['created_at'];
            $addAssessment->updated_at = $eachMarks['updated_at'];
            if ($addAssessment->save()) {
                return response()->json([
                    'success' => true,
                    'message' => "Assessment Marks Added Succesfully",

                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => "Unknown Error Occured !",

                ], 200);

            }

    };
}
public function getAccomplishedObjectives(){
    $marks = DB::table('lecturesoutline')
    ->join('assessments','assessments.outlineId','=','lecturesoutline.outlineId')
    ->join('assessment_marks','assessments.asId','=','assessment_marks.assessment_id')
    ->join('course_objectives','course_objectives.objName','=','lecturesoutline.objectives')
        // ->where('lecturesoutline.subject', '=', '')
    ->select('assessments.*','lecturesoutline.*','course_objectives.*')
    // ->groupBy('')
    ->distinct()
        ->get();
    return $marks;

}
     

            }
