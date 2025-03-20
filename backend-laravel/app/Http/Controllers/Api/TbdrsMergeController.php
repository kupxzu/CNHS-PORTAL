<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TbdrsMerge;
use App\Models\Track;
use App\Models\Building;
use App\Models\Department;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="TBDRS Merge",
 *     description="API Endpoints for Track-Building-Department-Room-Section Merge Management"
 * )
 */
class TbdrsMergeController extends Controller
{
    /**
     * @OA\Get(
     *     path="/tbdrs-merge",
     *     summary="Get all TBDRS merges",
     *     tags={"TBDRS Merge"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of TBDRS merges with relationships",
     *         @OA\JsonContent(
     *             @OA\Property(property="tbdrs_merges", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="track_id", type="integer", example=1),
     *                     @OA\Property(property="building_id", type="integer", example=1),
     *                     @OA\Property(property="department_id", type="integer", example=1),
     *                     @OA\Property(property="section_id", type="integer", example=1),
     *                     @OA\Property(property="created_at", type="string", format="date-time"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time"),
     *                     @OA\Property(property="track", type="object"),
     *                     @OA\Property(property="building", type="object"),
     *                     @OA\Property(property="department", type="object"),
     *                     @OA\Property(property="section", type="object")
     *                 )
     *             ),
     *             @OA\Property(property="message", type="string", example="TBDRS merges retrieved successfully")
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
        $tbdrsMerges = TbdrsMerge::with(['track', 'building', 'department', 'section'])->get();
        return response()->json([
            'tbdrs_merges' => $tbdrsMerges,
            'message' => 'TBDRS merges retrieved successfully'
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/tbdrs-merge",
     *     summary="Create a new TBDRS merge",
     *     tags={"TBDRS Merge"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"track_id", "building_id", "department_id", "section_id"},
     *             @OA\Property(property="track_id", type="integer", example=1),
     *             @OA\Property(property="building_id", type="integer", example=1),
     *             @OA\Property(property="department_id", type="integer", example=1),
     *             @OA\Property(property="section_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="TBDRS merge created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="tbdrs_merge", type="object"),
     *             @OA\Property(property="message", type="string", example="TBDRS merge created successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation errors"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="One or more referenced entities not found"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'track_id' => 'required|integer|exists:tracks,id',
            'building_id' => 'required|integer|exists:buildings,id',
            'department_id' => 'required|integer|exists:departments,id',
            'section_id' => 'required|integer|exists:sections,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Check if the combination already exists
        $existing = TbdrsMerge::where('track_id', $request->track_id)
            ->where('building_id', $request->building_id)
            ->where('department_id', $request->department_id)
            ->where('section_id', $request->section_id)
            ->first();

        if ($existing) {
            return response()->json([
                'message' => 'This TBDRS combination already exists',
                'tbdrs_merge' => $existing
            ], 422);
        }

        // Verify all referenced entities exist
        $track = Track::find($request->track_id);
        $building = Building::find($request->building_id);
        $department = Department::find($request->department_id);
        $section = Section::find($request->section_id);

        if (!$track || !$building || !$department || !$section) {
            return response()->json([
                'message' => 'One or more referenced entities not found'
            ], 404);
        }

        $tbdrsMerge = TbdrsMerge::create([
            'track_id' => $request->track_id,
            'building_id' => $request->building_id,
            'department_id' => $request->department_id,
            'section_id' => $request->section_id
        ]);

        // Load relationships for the response
        $tbdrsMerge->load(['track', 'building', 'department', 'section']);

        return response()->json([
            'tbdrs_merge' => $tbdrsMerge,
            'message' => 'TBDRS merge created successfully'
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/tbdrs-merge/{id}",
     *     summary="Get a specific TBDRS merge",
     *     tags={"TBDRS Merge"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="TBDRS Merge ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="TBDRS merge details with relationships",
     *         @OA\JsonContent(
     *             @OA\Property(property="tbdrs_merge", type="object"),
     *             @OA\Property(property="message", type="string", example="TBDRS merge retrieved successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="TBDRS merge not found"
     *     )
     * )
     */
    public function show($id)
    {
        $tbdrsMerge = TbdrsMerge::with(['track', 'building', 'department', 'section'])->find($id);
        
        if (!$tbdrsMerge) {
            return response()->json([
                'message' => 'TBDRS merge not found'
            ], 404);
        }

        return response()->json([
            'tbdrs_merge' => $tbdrsMerge,
            'message' => 'TBDRS merge retrieved successfully'
        ], 200);
    }

    /**
     * @OA\Put(
     *     path="/tbdrs-merge/{id}",
     *     summary="Update a TBDRS merge",
     *     tags={"TBDRS Merge"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="TBDRS Merge ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"track_id", "building_id", "department_id", "section_id"},
     *             @OA\Property(property="track_id", type="integer", example=1),
     *             @OA\Property(property="building_id", type="integer", example=1),
     *             @OA\Property(property="department_id", type="integer", example=1),
     *             @OA\Property(property="section_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="TBDRS merge updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="tbdrs_merge", type="object"),
     *             @OA\Property(property="message", type="string", example="TBDRS merge updated successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="TBDRS merge not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation errors"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $tbdrsMerge = TbdrsMerge::find($id);
        
        if (!$tbdrsMerge) {
            return response()->json([
                'message' => 'TBDRS merge not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'track_id' => 'required|integer|exists:tracks,id',
            'building_id' => 'required|integer|exists:buildings,id',
            'department_id' => 'required|integer|exists:departments,id',
            'section_id' => 'required|integer|exists:sections,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Check if the combination already exists (excluding this record)
        $existing = TbdrsMerge::where('track_id', $request->track_id)
            ->where('building_id', $request->building_id)
            ->where('department_id', $request->department_id)
            ->where('section_id', $request->section_id)
            ->where('id', '!=', $id)
            ->first();

        if ($existing) {
            return response()->json([
                'message' => 'This TBDRS combination already exists',
                'tbdrs_merge' => $existing
            ], 422);
        }

        $tbdrsMerge->update([
            'track_id' => $request->track_id,
            'building_id' => $request->building_id,
            'department_id' => $request->department_id,
            'section_id' => $request->section_id
        ]);

        // Load relationships for the response
        $tbdrsMerge->load(['track', 'building', 'department', 'section']);

        return response()->json([
            'tbdrs_merge' => $tbdrsMerge,
            'message' => 'TBDRS merge updated successfully'
        ], 200);
    }

    /**
     * @OA\Delete(
     *     path="/tbdrs-merge/{id}",
     *     summary="Delete a TBDRS merge",
     *     tags={"TBDRS Merge"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="TBDRS Merge ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="TBDRS merge deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="TBDRS merge deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="TBDRS merge not found"
     *     )
     * )
     */
    public function destroy($id)
    {
        $tbdrsMerge = TbdrsMerge::find($id);
        
        if (!$tbdrsMerge) {
            return response()->json([
                'message' => 'TBDRS merge not found'
            ], 404);
        }

        $tbdrsMerge->delete();

        return response()->json([
            'message' => 'TBDRS merge deleted successfully'
        ], 200);
    }
}