<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use Illuminate\Support\Facades\Session;

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

    /**
     * Processar o formulário de mensagem com captcha
     */
    public function sendMessage(Request $request)
    {
        // Validação dos dados
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'captcha_answer' => 'required|integer',
        ], [
            'name.required' => 'O nome é obrigatório.',
            'email.required' => 'O email é obrigatório.',
            'email.email' => 'Por favor, insira um email válido.',
            'subject.required' => 'O assunto é obrigatório.',
            'message.required' => 'A mensagem é obrigatória.',
            'captcha_answer.required' => 'Por favor, resolva a operação matemática.',
            'captcha_answer.integer' => 'A resposta deve ser um número.',
        ]);

        // Verificar captcha
        $captchaResult = Session::get('captcha_result');
        if (!$captchaResult || $request->captcha_answer != $captchaResult) {
            return back()->withErrors(['captcha_answer' => 'Resposta incorreta para a operação matemática.'])->withInput();
        }

        // Criar a mensagem
        Message::create([
            'name' => $request->name,
            'email' => $request->email,
            'subject' => $request->subject,
            'message' => $request->message,
            'phone' => $request->phone,
            'company' => $request->company,
            'is_read' => false,
            'is_replied' => false,
        ]);

        // Limpar captcha da sessão
        Session::forget('captcha_result');

        return back()->with('success', 'Mensagem enviada com sucesso! Entrarei em contato em breve.');
    }

    /**
     * Gerar novo captcha
     */
    public function generateCaptcha()
    {
        $num1 = rand(1, 10);
        $num2 = rand(1, 10);
        $result = $num1 + $num2;
        
        Session::put('captcha_result', $result);
        
        return response()->json([
            'question' => "$num1 + $num2 = ?",
            'num1' => $num1,
            'num2' => $num2
        ]);
    }
}