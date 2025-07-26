<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
}
