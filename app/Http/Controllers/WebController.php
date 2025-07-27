<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CourseCategory;
use Illuminate\Support\Facades\Auth;

class WebController extends Controller
{
    public function index()
    {
        $topCourses = [
            (object)[
                'title' => 'Intro to Web Development',
                'category' => 'Programming',
                'image_url' => '/assets/img/web-dev.jpg',
                'uploader' => 'John Doe'
            ],
            (object)[
                'title' => 'Graphic Design Basics',
                'category' => 'Design',
                'image_url' => '/assets/img/design.jpg',
                'uploader' => 'John Doe'
            ],
            (object)[
                'title' => 'Digital Marketing 101',
                'category' => 'Marketing',
                'image_url' => '/assets/img/marketing.jpg',
                'uploader' => 'John Doe'
            ],
        ];

        return view('index', compact('topCourses'));
    }

    public function login()
    {
        return view('auth.login');
    }

    public function register()
    {
        return view('auth.register');
    }

    public function dashboard() {
        return view('user.dashboard');
    }

    public function course() {
        return view('course');
    }

    public function createCourse() {
        $categories = CourseCategory::all(); // get all categories
        return view('user.create-course', compact('categories'));
    }

    public function detailCourse($id) {
        return view('user.detail-course', compact('id'));
    }

    public function adminDashboard(Request $request) {
        return view('admin.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
