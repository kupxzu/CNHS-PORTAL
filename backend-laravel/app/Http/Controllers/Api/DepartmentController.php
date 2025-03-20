<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Departments",
 *     description="API Endpoints for Department Management"
 * )
 */
class DepartmentController extends Controller
{
    /**
     * @OA\Get(
     *     path="/departments",
     *     summary="Get all departments",
     *     tags={"Departments"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of departments",
     *         @OA\JsonContent(
     *             @OA\Property(property="departments", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="department_name", type="string", example="STEM"),
     *                     @OA\Property(property="created_at", type="string", format="date-time"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time")
     *                 )
     *             ),
     *             @OA\Property(property="message", type="string", example="Departments retrieved successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function index()
    {
        $departments = Department::all();
        return response()->json([
            'departments' => $departments,
            'message' => 'Departments retrieved successfully'
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/departments",
     *     summary="Create a new department",
     *     tags={"Departments"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"department_name"},
     *             @OA\Property(property="department_name", type="string", example="ICT")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Department created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="department", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="department_name", type="string", example="ICT"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             ),
     *             @OA\Property(property="message", type="string", example="Department created successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation errors"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'department_name' => 'required|string|max:255|unique:departments'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $department = Department::create([
            'department_name' => $request->department_name
        ]);

        return response()->json([
            'department' => $department,
            'message' => 'Department created successfully'
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/departments/{id}",
     *     summary="Get a specific department",
     *     tags={"Departments"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Department ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Department details",
     *         @OA\JsonContent(
     *             @OA\Property(property="department", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="department_name", type="string", example="STEM"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             ),
     *             @OA\Property(property="message", type="string", example="Department retrieved successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Department not found"
     *     )
     * )
     */
    public function show($id)
    {
        $department = Department::find($id);
        
        if (!$department) {
            return response()->json([
                'message' => 'Department not found'
            ], 404);
        }

        return response()->json([
            'department' => $department,
            'message' => 'Department retrieved successfully'
        ], 200);
    }

    /**
     * @OA\Put(
     *     path="/departments/{id}",
     *     summary="Update a department",
     *     tags={"Departments"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Department ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"department_name"},
     *             @OA\Property(property="department_name", type="string", example="Updated Department Name")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Department updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="department", type="object"),
     *             @OA\Property(property="message", type="string", example="Department updated successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Department not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation errors"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $department = Department::find($id);
        
        if (!$department) {
            return response()->json([
                'message' => 'Department not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'department_name' => 'required|string|max:255|unique:departments,department_name,' . $id
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $department->update([
            'department_name' => $request->department_name
        ]);

        return response()->json([
            'department' => $department,
            'message' => 'Department updated successfully'
        ], 200);
    }

    /**
     * @OA\Delete(
     *     path="/departments/{id}",
     *     summary="Delete a department",
     *     tags={"Departments"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Department ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Department deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Department deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Department not found"
     *     )
     * )
     */
    public function destroy($id)
    {
        $department = Department::find($id);
        
        if (!$department) {
            return response()->json([
                'message' => 'Department not found'
            ], 404);
        }

        $department->delete();

        return response()->json([
            'message' => 'Department deleted successfully'
        ], 200);
    }
}