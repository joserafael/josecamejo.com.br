<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CaptchaService;

class ExampleFormController extends Controller
{
    /**
     * Exibe o formulário de exemplo
     */
    public function showForm()
    {
        return view('example-form');
    }

    /**
     * Processa o formulário de exemplo
     */
    public function submitForm(Request $request)
    {
        // Validação básica
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'message' => 'required|string',
            'captcha_answer' => 'required|integer',
        ]);

        // Verificar captcha usando o CaptchaService
        if (!CaptchaService::validate($request->captcha_answer)) {
            return back()->withErrors(['captcha_answer' => 'Resposta incorreta para a operação matemática.'])->withInput();
        }

        // Processar os dados do formulário aqui
        // ...

        // Limpar captcha da sessão
        CaptchaService::clear();

        return back()->with('success', 'Formulário enviado com sucesso!');
    }

    /**
     * Gerar novo captcha para este formulário
     */
    public function generateCaptcha()
    {
        return CaptchaService::generateJson();
    }
}