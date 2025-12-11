<?php

namespace BuildForge\Controllers;

class HomeController extends Controller
{
    public function index(): void
    {
        $this->render('home/index', ['title' => 'Home']);
    }
}
