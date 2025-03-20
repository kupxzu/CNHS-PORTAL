<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StdTbdrsMerge;
use App\Models\TbdrsMerge;
use App\Models\UUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Student TBDRS Merge",
 *     description="API Endpoints for Student-TBDRS Merge Management"
 * )
 */
class StdTbdrsMergeController extends Controller
{
    /**
     * @OA\Get(
     *     path="/std-tbdrs-merge",
     *     summary="Get all student TBDRS merges",
     *     tags={"Student TBDRS Merge"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of student TBDRS merges with relationships",
     *         @OA\JsonContent(
     *             @OA\Property(property="std_tbdrs_merges", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="uusers_id", type="integer", example=1),
     *                     @OA\Property(property="tbdrs_id", type="integer", example=1),
     *                     @OA\Property(property="created_at", type="string", format="date-time"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time"),
     *                     @OA\Property(property="user", type="object"),
     *                     @OA\Property(property="tbdrs_merge", type="object")
     *                 )
     *             ),
     *             @OA\Property(property="message", type="string", example="Student TBDRS merges retrieved successfully")
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
        $stdTbdrsMerges = StdTbdrsMerge::with([
            'user',
            'tbdrsMerge',
            'tbdrsMerge.track',
            'tbdrsMerge.building',
            'tbdrsMerge.department',
            'tbdrsMerge.section'
        ])->get();
        
        return response()->json([
            'std_tbdrs_merges' => $stdTbdrsMerges,
            'message' => 'Student TBDRS merges retrieved successfully'
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/std-tbdrs-merge",
     *     summary="Create a new student TBDRS merge",
     *     tags={"Student TBDRS Merge"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"uusers_id", "tbdrs_id"},
     *             @OA\Property(property="uusers_id", type="integer", example=1),
     *             @OA\Property(property="tbdrs_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Student TBDRS merge created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="std_tbdrs_merge", type="object"),
     *             @OA\Property(property="message", type="string", example="Student TBDRS merge created successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation errors"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User or TBDRS merge not found"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'uusers_id' => 'required|integer|exists:u_users,id',
            'tbdrs_id' => 'required|integer|exists:tbdrs_merge,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Check if user is a student
        $user = UUser::find($request->uusers_id);
        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        if ($user->role !== 'student') {
            return response()->json([
                'message' => 'Only students can be assigned to TBDRS'
            ], 422);
        }

        // Check if TBDRS merge exists
        $tbdrsMerge = TbdrsMerge::find($request->tbdrs_id);
        if (!$tbdrsMerge) {
            return response()->json([
                'message' => 'TBDRS merge not found'
            ], 404);
        }

        // Check if combination already exists
        $existing = StdTbdrsMerge::where('uusers_id', $request->uusers_id)
            ->where('tbdrs_id', $request->tbdrs_id)
            ->first();

        if ($existing) {
            return response()->json([
                'message' => 'This student is already assigned to this TBDRS',
                'std_tbdrs_merge' => $existing
            ], 422);
        }

        $stdTbdrsMerge = StdTbdrsMerge::create([
            'uusers_id' => $request->uusers_id,
            'tbdrs_id' => $request->tbdrs_id
        ]);

        // Load relationships for the response
        $stdTbdrsMerge->load([
            'user',
            'tbdrsMerge',
            'tbdrsMerge.track',
            'tbdrsMerge.building',
            'tbdrsMerge.department',
            'tbdrsMerge.section'
        ]);

        return response()->json([
            'std_tbdrs_merge' => $stdTbdrsMerge,
            'message' => 'Student TBDRS merge created successfully'
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/std-tbdrs-merge/{id}",
     *     summary="Get a specific student TBDRS merge",
     *     tags={"Student TBDRS Merge"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Student TBDRS Merge ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Student TBDRS merge details with relationships",
     *         @OA\JsonContent(
     *             @OA\Property(property="std_tbdrs_merge", type="object"),
     *             @OA\Property(property="message", type="string", example="Student TBDRS merge retrieved successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Student TBDRS merge not found"
     *     )
     * )
     */
    public function show($id)
    {
        $stdTbdrsMerge = StdTbdrsMerge::with([
            'user',
            'tbdrsMerge',
            'tbdrsMerge.track',
            'tbdrsMerge.building',
            'tbdrsMerge.department',
            'tbdrsMerge.section'
        ])->find($id);
        
        if (!$stdTbdrsMerge) {
            return response()->json([
                'message' => 'Student TBDRS merge not found'
            ], 404);
        }

        return response()->json([
            'std_tbdrs_merge' => $stdTbdrsMerge,
            'message' => 'Student TBDRS merge retrieved successfully'
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/std-tbdrs-merge/student/{userId}",
     *     summary="Get all TBDRS merges for a specific student",
     *     tags={"Student TBDRS Merge"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
     *         required=true,
     *         description="User ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of TBDRS merges for the student",
     *         @OA\JsonContent(
     *             @OA\Property(property="std_tbdrs_merges", type="array",
     *                 @OA\Items(type="object")
     *             ),
     *             @OA\Property(property="message", type="string", example="Student TBDRS merges retrieved successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found or not a student"
     *     )
     * )
     */
    public function getByStudent($userId)
    {
        // Check if user exists and is a student
        $user = UUser::find($userId);
        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        if ($user->role !== 'student') {
            return response()->json([
                'message' => 'User is not a student'
            ], 422);
        }

        $stdTbdrsMerges = StdTbdrsMerge::with([
            'user',
            'tbdrsMerge',
            'tbdrsMerge.track',
            'tbdrsMerge.building',
            'tbdrsMerge.department',
            'tbdrsMerge.section'
        ])->where('uusers_id', $userId)->get();

        return response()->json([
            'std_tbdrs_merges' => $stdTbdrsMerges,
            'message' => 'Student TBDRS merges retrieved successfully'
        ], 200);
    }

    /**
     * @OA\Delete(
     *     path="/std-tbdrs-merge/{id}",
     *     summary="Delete a student TBDRS merge",
     *     tags={"Student TBDRS Merge"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Student TBDRS Merge ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Student TBDRS merge deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Student TBDRS merge deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Student TBDRS merge not found"
     *     )
     * )
     */
    public function destroy($id)
    {
        $stdTbdrsMerge = StdTbdrsMerge::find($id);
        
        if (!$stdTbdrsMerge) {
            return response()->json([
                'message' => 'Student TBDRS merge not found'
            ], 404);
        }

        $stdTbdrsMerge->delete();

        return response()->json([
            'message' => 'Student TBDRS merge deleted successfully'
        ], 200);
    }
}