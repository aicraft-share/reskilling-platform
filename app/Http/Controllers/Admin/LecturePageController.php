<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LecturePage;
use App\Services\AdminOperationLogger;
use Illuminate\Http\Request;

class LecturePageController extends Controller
{
    public function index()
    {
        // Get all materials grouped by course
        $courses = \App\Models\Course::with(['lecturePages' => function($query) {
                $query->orderBy('sort_order', 'asc');
            }])
            ->orderBy('sort_order', 'asc')
            ->get();

        // Also get materials without a course just in case
        $orphanLectures = LecturePage::whereNull('course_id')
            ->orderBy('sort_order', 'asc')
            ->get();

        return view('admin.lecture_pages.index', compact('courses', 'orphanLectures'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $courses = \App\Models\Course::orderBy('sort_order', 'asc')->get();
        $nextSortOrder = LecturePage::max('sort_order') + 1;
        return view('admin.lecture_pages.create', compact('courses', 'nextSortOrder'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, AdminOperationLogger $operationLogger)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'section_name' => 'nullable|string|max:255',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'sometimes|boolean',
            'youtube_url' => 'nullable|url|max:500',
            'sort_order' => 'required|integer|min:0',
        ]);

        $data = [
            'course_id' => $request->course_id,
            'section_name' => $request->section_name ?? '基本',
            'title' => $request->title,
            'description' => $request->description,
            'sort_order' => $request->sort_order,
            'is_active' => $request->has('is_active') ? $request->is_active : true,
            'youtube_url' => $request->youtube_url,
            'youtube_video_id' => $this->extractYoutubeVideoId($request->youtube_url),
        ];

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail_path'] = $request->file('thumbnail')->store('thumbnails', 'public');
        }

        $lecturePage = LecturePage::create($data);

        $operationLogger->log(
            'create',
            'curriculum',
            $lecturePage->id,
            $lecturePage->title,
            [],
            $lecturePage->fresh()->toArray()
        );

        return redirect()->route('admin.lecture-pages.index')
            ->with('success', '教材を作成しました。');
    }

    /**
     * Display the specified resource.
     */
    public function show(LecturePage $lecturePage)
    {
        return view('admin.lecture_pages.show', compact('lecturePage'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LecturePage $lecturePage)
    {
        $courses = \App\Models\Course::orderBy('sort_order', 'asc')->get();
        return view('admin.lecture_pages.edit', compact('lecturePage', 'courses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, LecturePage $lecturePage, AdminOperationLogger $operationLogger)
    {
        $before = $lecturePage->toArray();

        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'section_name' => 'nullable|string|max:255',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'sometimes|boolean',
            'sort_order' => 'required|integer|min:0',
            'youtube_url' => 'nullable|url|max:500',
        ]);

        $data = $request->only(['course_id', 'section_name', 'title', 'description', 'sort_order', 'youtube_url']);
        $data['is_active'] = $request->has('is_active');
        $data['section_name'] = $data['section_name'] ?? '基本';
        $data['youtube_video_id'] = $this->extractYoutubeVideoId($request->youtube_url);

        if ($request->hasFile('thumbnail')) {
            // Delete old thumbnail
            if ($lecturePage->thumbnail_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($lecturePage->thumbnail_path);
            }
            $data['thumbnail_path'] = $request->file('thumbnail')->store('thumbnails', 'public');
        }

        $lecturePage->update($data);

        $operationLogger->log(
            'update',
            'curriculum',
            $lecturePage->id,
            $lecturePage->title,
            $before,
            $lecturePage->fresh()->toArray()
        );

        return redirect()->route('admin.lecture-pages.index')
            ->with('success', '教材を更新しました。');
    }

    public function destroy(LecturePage $lecturePage, AdminOperationLogger $operationLogger)
    {
        $before = $lecturePage->toArray();
        $lecturePage->delete();
        $operationLogger->log('delete', 'curriculum', $lecturePage->id, $lecturePage->title, $before, []);
        return redirect()->route('admin.lecture-pages.index')->with('success', '教材を削除しました。');
    }

    public function deactivate(LecturePage $lecturePage, AdminOperationLogger $operationLogger)
    {
        $before = $lecturePage->only([
            'title',
            'sort_order',
            'is_active',
        ]);

        $lecturePage->update([
            'is_active' => false,
            'sort_order' => 0,
        ]);

        $operationLogger->log(
            'status_change',
            'curriculum',
            $lecturePage->id,
            $lecturePage->title,
            $before,
            $lecturePage->fresh()->only(array_keys($before))
        );

        return redirect()->route('admin.lecture-pages.index')->with('success', '講義ページを停止（非公開）にし、表示順を0にしました。');
    }

    protected function extractYoutubeVideoId(?string $url): ?string
    {
        if (!$url)
            return null;
        preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/\s]{11})%i', $url, $match);
        return $match[1] ?? null;
    }
}
