<?php

namespace App\Controllers;

use App\Models\CourseModel;

class Home extends BaseController
{

    protected $courseModel;

    public function __construct()
    {
        $this->courseModel = new CourseModel();
    }

    public function index(): string
    {
        $featuredCourses = $this->courseModel->getFeaturedCourses();

        $data['featuredCourses'] = $featuredCourses;

        log_message('debug', 'Featured Courses: ' . json_encode($featuredCourses));

        return view('beranda/index', $data);
    }

    public function belajar_segment()
    {
        $uri = service('uri');
        $parameter1 = $uri->getSegment(3);
        $parameter2 = $uri->getSegment(4);
        $parameter3 = $uri->getSegment(5);

        $data['p1'] = $parameter1;
        $data['p2'] = $parameter2;
        $data['p3'] = $parameter3;

        return view('segment_view', $data);
    }
}
