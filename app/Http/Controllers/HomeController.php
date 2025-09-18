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
                    'description' => 'Desenvolvimento ágil de aplicações web robustas'
                ],
                [
                    'name' => 'PHP (CodeIgniter, CakePHP, Laravel)',
                    'description' => 'Criação de sistemas escaláveis e APIs RESTful'
                ],
                [
                    'name' => 'Python (Django, Flask)',
                    'description' => 'Automação, análise de dados e web development'
                ],
                [
                    'name' => 'JavaScript & Vue.js',
                    'description' => 'Interfaces modernas e experiências interativas'
                ],
                [
                    'name' => 'MySQL & PostgreSQL',
                    'description' => 'Otimização e modelagem de bancos de dados'
                ],
                [
                    'name' => 'Docker',
                    'description' => 'Containerização e deploy de aplicações'
                ],
                [
                    'name' => 'Git & GitHub',
                    'description' => 'Controle de versão e colaboração em equipe'
                ]
            ],
            'social' => [
                'github' => 'https://github.com/joserafael',
                'linkedin' => 'https://www.linkedin.com/in/jose-rafael-camejo/',
                'email' => 'contato@josecamejo.com.br'
            ]
        ];

        return view('home', compact('data'));
    }
}