<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseComment;
use App\Models\CourseLike;
use App\Models\CourseMaterial;
use Illuminate\Support\Facades\Auth;
use Validator;

class CourseController extends Controller
{
    public function courses() {
        $courses = Course::with(['materials', 'likes', 'comments', 'categories'])
            ->where('is_approved', true)
            ->get()
            ->map(function ($course) {
                return [
                    'id' => $course->id,
                    'title' => $course->title,
                    'description' => $course->description,
                    'thumbnail' => $course->thumbnail,
                    'likes_count' => $course->likes_count,
                    'categories' => $course->categories->pluck('name'), // assuming 'name' column exists
                    'materials_count' => $course->materials->count(),
                    'comments_count' => $course->comments->count(),
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => $courses
        ]);
    }

    public function approvedCourses(Request $request)
    {
        $userId = $request->input('user_id');

        if (!$userId) {
            return response()->json(['status' => 'error', 'message' => 'User ID is required'], 400);
        }

        $courses = Course::with(['materials', 'likes', 'comments', 'categories'])
            ->where('user_id', $userId)
            ->where('is_approved', true)
            ->get()
            ->map(function ($course) {
                return [
                    'id' => $course->id,
                    'title' => $course->title,
                    'description' => $course->description,
                    'thumbnail' => $course->thumbnail,
                    'likes_count' => $course->likes_count,
                    'categories' => $course->categories->pluck('name'), // assuming 'name' column exists
                    'materials_count' => $course->materials->count(),
                    'comments_count' => $course->comments->count(),
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => $courses
        ]);
    }

    public function courseDetail($id)
    {
        $course = Course::with(['user:id,name', 'materials', 'categories:id,name'])
            ->where('id', $id)
            ->where('is_approved', true)
            ->first();

        if (!$course) {
            return response()->json([
                'status' => 'error',
                'message' => 'Course not found or not approved.'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => $course->id,
                'title' => $course->title,
                'description' => $course->description,
                'thumbnail' => $course->thumbnail,
                'creator' => $course->user->name,
                'categories' => $course->categories->pluck('name'),
                'materials' => $course->materials->map(function ($material) {
                    return [
                        'title' => $material->title,
                        'file_type' => $material->file_type,
                        'path_file' => $material->file_type === 'pdf'
                            ? asset('assets/materials/' . $material->file_path)
                            : $material->file_path,
                    ];
                }),
                'likes_count' => $course->likes_count,
                'comments_count' => $course->comments->count(),
                'comments' => $course->comments->map(function ($comment) {
                    return [
                        'user' => $comment->user->name,
                        'comment' => $comment->comment,
                        'created_at' => $comment->created_at->diffForHumans(),
                    ];
                }),
            ]
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'thumbnail' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'category_id' => 'required|exists:course_categories,id',
            'material_type' => 'required|in:pdf,video',
            'material_file' => 'required_if:material_type,pdf|file|mimes:pdf|max:5120',
            'material_link' => 'required_if:material_type,video|url',
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        // === Save Thumbnail ===
        $thumbnail = $request->file('thumbnail');
        $thumbnailFileName = time() . '_' . $thumbnail->getClientOriginalName();
        $thumbnail->move(public_path('assets/img'), $thumbnailFileName);

        // === Create Course ===
        $course = Course::create([
            'user_id' => $request->user_id,
            'title' => $request->title,
            'description' => $request->description,
            'thumbnail' => $thumbnailFileName,
            'is_approved' => false,
        ]);

        // === Attach Category ===
        $course->categories()->attach($request->category_id);

        // === Handle Material ===
        if ($request->material_type === 'pdf') {
            $pdf = $request->file('material_file');
            $pdfFileName = time() . '_' . $pdf->getClientOriginalName();
            $pdf->move(public_path('assets/materials'), $pdfFileName);

            CourseMaterial::create([
                'course_id' => $course->id,
                'title' => 'Introduction (PDF)',
                'file_path' => $pdfFileName,
                'file_type' => 'pdf',
            ]);
        } else {
            CourseMaterial::create([
                'course_id' => $course->id,
                'title' => 'Introduction (Video)',
                'file_path' => $request->material_link,
                'file_type' => 'video',
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Course created. Pending approval.',
            'data' => $course
        ]);
    }

    public function edit($id)
    {
        $course = Course::with('categories')->findOrFail($id);

        // Optional: check if the authenticated user is allowed to edit
        if ($course->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        return response()->json([
            'course' => $course,
            'categories' => CourseCategory::all(),
        ]);
    }

    public function update(Request $request, $id)
    {
        $course = Course::where('id', $id)->where('user_id', Auth::id())->first();

        if (!$course) {
            return response()->json(['status' => 'error', 'message' => 'Course not found or not yours.'], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'thumbnail' => 'sometimes|image|mimes:jpg,jpeg,png|max:2048',
            'category_id' => 'sometimes|required|exists:course_categories,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        if ($request->hasFile('thumbnail')) {
            Storage::delete('public/thumbnails/' . $course->thumbnail);
            $path = $request->file('thumbnail')->store('public/thumbnails');
            $course->thumbnail = basename($path);
        }

        if ($request->has('title')) $course->title = $request->title;
        if ($request->has('description')) $course->description = $request->description;

        $course->is_approved = false; // Reset approval
        $course->save();

        // Update category
        if ($request->has('category_id')) {
            $course->categories()->sync([$request->category_id]);
        }

        return response()->json(['status' => 'success', 'message' => 'Course updated.', 'data' => $course]);
    }

    public function destroy($id)
    {
        $course = Course::where('id', $id)->where('user_id', Auth::id())->first();

        if (!$course) {
            return response()->json(['status' => 'error', 'message' => 'Course not found or not yours.'], 404);
        }

        // Delete thumbnail
        Storage::delete('public/thumbnails/' . $course->thumbnail);

        // Detach category
        $course->categories()->detach();

        // Delete materials
        $course->materials()->delete();

        // Delete course
        $course->delete();

        return response()->json(['status' => 'success', 'message' => 'Course deleted.']);
    }

    public function postComment(Request $request, $id)
    {
        $request->validate([
            'comment' => 'required|string|max:1000',
            'user_id' => 'required|exists:users,id'
        ]);

        $course = Course::where('id', $id)->where('is_approved', true)->first();

        if (!$course) {
            return response()->json([
                'status' => 'error',
                'message' => 'Course not found or not approved.'
            ], 404);
        }

        $comment = new CourseComment();
        $comment->course_id = $course->id;
        $comment->user_id = $request->input('user_id');
        $comment->comment = $request->comment;
        $comment->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Comment posted successfully.'
        ]);
    }

}
