<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Sections",
 *     description="API Endpoints for Section Management"
 * )
 */
class SectionController extends Controller
{
    /**
     * @OA\Get(
     *     path="/sections",
     *     summary="Get all sections",
     *     tags={"Sections"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of sections",
     *         @OA\JsonContent(
     *             @OA\Property(property="sections", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="section_name", type="string", example="Section A"),
     *                     @OA\Property(property="created_at", type="string", format="date-time"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time")
     *                 )
     *             ),
     *             @OA\Property(property="message", type="string", example="Sections retrieved successfully")
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
        $sections = Section::all();
        return response()->json([
            'sections' => $sections,
            'message' => 'Sections retrieved successfully'
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/sections",
     *     summary="Create a new section",
     *     tags={"Sections"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"section_name"},
     *             @OA\Property(property="section_name", type="string", example="Section B")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Section created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="section", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="section_name", type="string", example="Section B"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             ),
     *             @OA\Property(property="message", type="string", example="Section created successfully")
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
            'section_name' => 'required|string|max:255|unique:sections'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $section = Section::create([
            'section_name' => $request->section_name
        ]);

        return response()->json([
            'section' => $section,
            'message' => 'Section created successfully'
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/sections/{id}",
     *     summary="Get a specific section",
     *     tags={"Sections"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Section ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Section details",
     *         @OA\JsonContent(
     *             @OA\Property(property="section", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="section_name", type="string", example="Section A"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             ),
     *             @OA\Property(property="message", type="string", example="Section retrieved successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Section not found"
     *     )
     * )
     */
    public function show($id)
    {
        $section = Section::find($id);
        
        if (!$section) {
            return response()->json([
                'message' => 'Section not found'
            ], 404);
        }

        return response()->json([
            'section' => $section,
            'message' => 'Section retrieved successfully'
        ], 200);
    }

    /**
     * @OA\Put(
     *     path="/sections/{id}",
     *     summary="Update a section",
     *     tags={"Sections"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Section ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"section_name"},
     *             @OA\Property(property="section_name", type="string", example="Updated Section Name")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Section updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="section", type="object"),
     *             @OA\Property(property="message", type="string", example="Section updated successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Section not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation errors"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $section = Section::find($id);
        
        if (!$section) {
            return response()->json([
                'message' => 'Section not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'section_name' => 'required|string|max:255|unique:sections,section_name,' . $id
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $section->update([
            'section_name' => $request->section_name
        ]);

        return response()->json([
            'section' => $section,
            'message' => 'Section updated successfully'
        ], 200);
    }

    /**
     * @OA\Delete(
     *     path="/sections/{id}",
     *     summary="Delete a section",
     *     tags={"Sections"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Section ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Section deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Section deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Section not found"
     *     )
     * )
     */
    public function destroy($id)
    {
        $section = Section::find($id);
        
        if (!$section) {
            return response()->json([
                'message' => 'Section not found'
            ], 404);
        }

        $section->delete();

        return response()->json([
            'message' => 'Section deleted successfully'
        ], 200);
    }
}