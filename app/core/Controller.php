<?php

namespace App\core;

class Controller
{
    protected $view;

    public function __construct()
    {
        $this->view = new View();
    }

    protected function render(string $view, array $data = []): void
    {
        View::render($view, $data);
    }


        protected
        function view($view, $data = [])
        {
            $this->view->render($view, $data);
        }


        protected
        function redirect($url)
        {
            $cleanedUrl = ltrim($url, '/');

            header("Location: /" . $cleanedUrl);
            exit;
        }
    }