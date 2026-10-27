<?php

namespace App\Controllers;

class Dashboard extends BaseController
{
    public function index(): string
    {
        $data = [
            'title'       => 'Dashboard - Panel Alternate',
            'plugins_css' => [
                'plugins/vectormap/jquery-jvectormap-2.0.2.css',
            ],
            'plugins_js'  => [
                'plugins/vectormap/jquery-jvectormap-2.0.2.min.js',
                'plugins/vectormap/jquery-jvectormap-world-mill-en.js',
                'plugins/chartjs/js/chart.js',
                'plugins/sparkline-charts/jquery.sparkline.min.js',
                'plugins/raphael/raphael-min.js',
                'plugins/morris/js/morris.js',
            ],
            'page_js'     => 'index2.js',
        ];

        return view('pages/dashboard/index', $data);
    }
}
