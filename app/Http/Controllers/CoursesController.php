<?php

namespace App\Http\Controllers;

use App\Models\CourseInstructorsModel;
use App\Models\CoursesModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;


class CoursesController extends Controller
{
    //
    
    public function getAllCourses()
    {
        $availableCourses = DB::table('courses')
            ->leftJoin('course_instructors', 'courses.courseId', '=', 'course_instructors.courseId')
            ->where('course_instructors.courseId', '=', null)
            // ->where('courses.courseId', 'NOT IN', '(SELECT course_instructors.courseId FROM course_instructors)')
            ->select('courses.*')
            // ->select('courses.*', 'course_instructors.*')
            ->get();
        return $availableCourses;
        // return CoursesModel::all();
    }
    public function getAllAssignedCourses()
    {
        $availableCourses = DB::table('course_instructors')
            ->join('users', 'course_instructors.instructor_userId', '=', 'users.userId')
            ->join('courses', 'courses.courseId', '=', 'course_instructors.courseId')
            // ->where('course_instructors.courseId', '=', null)
            // ->where('courses.courseId', 'NOT IN', '(SELECT course_instructors.courseId FROM course_instructors)')
            ->select('courses.*', 'course_instructors.semester', 'courses.courseCrHR','course_instructors.department','course_instructors.session', 'users.*')
            // ->select('courses.*', 'course_instructors.*')
            ->get();
        return $availableCourses;
        // return CoursesModel::all();
    }
    public function addNewLecture(Request $req){
        return response()->json([
            'success' => true,
            'message' => "no",

        ], 200);

    }
    public function addCourse(Request $req)
    {
        $validator = Validator::make(
            $req->all(),
            [
                'courseName' => 'required|string|min:3|max:30',
                'courseCode' => 'required|string|min:3|max:30',
                'courseCrHr' => 'required|max:1',
                'courseDesc' => 'required|string|min:10'
            ]
        );
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => "Field Values are not valid  !"], 200);
        } else {
            $alreadyExits = CoursesModel::where('courseCode', $req->input('courseCode'))->first();
            if (!$alreadyExits) {
                $addCourse = new CoursesModel();
                $addCourse->courseName = $req->input('courseName');
                $addCourse->courseCode = $req->input('courseCode');
                $addCourse->courseCrHr = $req->input('courseCrHr');
                $addCourse->courseDesc = $req->input('courseDesc');
                $addCourse->created_at = $req->input('created_at');
                $addCourse->updated_at = $req->input('updated_at');
                if ($addCourse->save()) {
                    return response()->json([
                        'success' => true,
                        'message' => "Course Added Succesfully",

                    ], 200);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => "Unknown Error Occured !",

                    ], 200);
                }
            } else {
                return response()->json(['success' => false, 'message' => "Course with this course code Already Exits !"], 200);
            }
        }
    }
    public function assignCourses(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'courseId' => 'required|integer',
            'instructor_userId' => 'required|integer',
            'semester' => 'required|integer',
            'department' => 'required|string',
            'session' => 'required|string|min:4',
            'created_at' => 'required',
            'updated_at' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => "Field Values are not valid  !"], 200);
        } else {
            $loggedInUserId = auth()->user()->userId;
            $course_instructors_table = new CourseInstructorsModel();
            $course_instructors_table->courseId = $req->input('courseId');
            $course_instructors_table->instructor_userId = $req->input('instructor_userId');
            $course_instructors_table->assigned_by = $loggedInUserId;
            $course_instructors_table->semester = $req->input('semester');
            $course_instructors_table->department = $req->input('department');
            $course_instructors_table->session = $req->input('session');
            $course_instructors_table->created_at = $req->input('created_at');
            $course_instructors_table->updated_at = $req->input('updated_at');
            if ($course_instructors_table->save()) {
                return response()->json([
                    'success' => true,
                    'message' => "Course Assigned Successfully !",

                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => "Unknown Error Occured !",

                ], 200);
            }
        }
    }
    public function getLoggedInUserCourses()
    {

        $loggedInUserId = auth()->user()->userId;
        // $authUserInfo = auth()->user()->fName;
        $myCourses = DB::table('courses')
            ->join('course_instructors', 'course_instructors.courseId', '=', 'courses.courseId')
            ->where('course_instructors.instructor_userId', '=', $loggedInUserId)
            ->select('courses.*', 'course_instructors.*')
            ->get();

        return $myCourses;
        // return response()->json(['success' => false, 'message' => "$authUserInfo"], 200);
    }
}
