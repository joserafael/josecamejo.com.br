<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use Illuminate\Support\Facades\Session;
use App\Services\CaptchaService;

class HomeController extends Controller
{
    /**
     * Exibe a landing page principal com apresentação pessoal
     */
    public function index()
    {
        $data = [
            'name' => 'José Rafael Camejo',
            'title' => __('messages.full_stack_developer'),
            'description' => __('messages.passionate_about_technology'),
            'profile_image' => file_exists(public_path('images/profile.jpg')) ? '/images/profile.jpg' : '/images/profile.svg',
            'skills' => [
                [
                    'name' => 'Ruby & Ruby on Rails',
                    'description' => __('messages.skill_ruby_description'),
                    'icon' => 'fas fa-gem'
                ],
                [
                    'name' => 'PHP (CodeIgniter, CakePHP, Laravel)',
                    'description' => __('messages.skill_php_description'),
                    'icon' => 'fab fa-php'
                ],
                [
                    'name' => 'Python (Django, Flask)',
                    'description' => __('messages.skill_python_description'),
                    'icon' => 'fab fa-python'
                ],
                [
                    'name' => 'JavaScript (Vue.js, React, Node.js)',
                    'description' => __('messages.skill_javascript_description'),
                    'icon' => 'fab fa-vuejs'
                ], 
                [
                    'name' => 'MySQL & PostgreSQL',
                    'description' => __('messages.skill_database_description'),
                    'icon' => 'fas fa-database'
                ],
                [
                    'name' => 'Docker',
                    'description' => __('messages.skill_docker_description'),
                    'icon' => 'fab fa-docker'
                ],
                [
                    'name' => 'Git & GitHub',
                    'description' => __('messages.skill_git_description'),
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
            'name.required' => __('messages.validation.name_required'),
            'email.required' => __('messages.validation.email_required'),
            'email.email' => __('messages.validation.email_invalid'),
            'subject.required' => __('messages.validation.subject_required'),
            'message.required' => __('messages.validation.message_required'),
            'captcha_answer.required' => __('messages.validation.captcha_required'),
            'captcha_answer.integer' => __('messages.validation.captcha_integer'),
        ]);

        // Verificar captcha
        if (!CaptchaService::validate($request->captcha_answer)) {
            return back()->withErrors(['captcha_answer' => __('messages.validation.captcha_incorrect')])->withInput();
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
        CaptchaService::clear();

        return back()->with('success', __('messages.success.message_sent'));
    }

    /**
     * Gerar novo captcha
     */
    public function generateCaptcha()
    {
        return CaptchaService::generateJson();
    }

    /**
     * Exibir a política de privacidade
     */
    public function privacyPolicy()
    {
        return view('privacy-policy');
    }
}