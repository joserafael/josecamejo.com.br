<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Exibe a landing page principal com apresentação pessoal
     */
    public function index()
    {
        $data = [
            'name' => 'José Rafael Camejo',
            'title' => 'Desenvolvedor Full Stack',
            'description' => 'Apaixonado por tecnologia e desenvolvimento de soluções inovadoras',
            'profile_image' => file_exists(public_path('images/profile.jpg')) ? '/images/profile.jpg' : '/images/profile.svg',
            'skills' => [
                [
                    'name' => 'Ruby & Ruby on Rails',
                    'description' => 'Desenvolvimento ágil de aplicações web robustas',
                    'icon' => 'fas fa-gem'
                ],
                [
                    'name' => 'PHP (CodeIgniter, CakePHP, Laravel)',
                    'description' => 'Criação de sistemas escaláveis e APIs RESTful',
                    'icon' => 'fab fa-php'
                ],
                [
                    'name' => 'Python (Django, Flask)',
                    'description' => 'Automação, análise de dados e web development',
                    'icon' => 'fab fa-python'
                ],
                [
                    'name' => 'JavaScript (Vue.js, React, Node.js)',
                    'description' => 'Interfaces modernas e experiências interativas',
                    'icon' => 'fab fa-vuejs'
                ], 
                [
                    'name' => 'MySQL & PostgreSQL',
                    'description' => 'Otimização e modelagem de bancos de dados',
                    'icon' => 'fas fa-database'
                ],
                [
                    'name' => 'Docker',
                    'description' => 'Containerização e deploy de aplicações',
                    'icon' => 'fab fa-docker'
                ],
                [
                    'name' => 'Git & GitHub',
                    'description' => 'Controle de versão e colaboração em equipe',
                    'icon' => 'fab fa-git-alt'
                ]
            ],
            'social' => [
                'github' => 'https://github.com/joserafael',
                'linkedin' => 'https://www.linkedin.com/in/jose-rafael-camejo/',
                'bluesky' => 'https://bsky.app/profile/josecamejo.com.br',
                'X' => 'https://www.x.com/joserafael'
            ]
        ];

        return view('home', compact('data'));
    }
}