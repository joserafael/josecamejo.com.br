<?php

namespace App\Services;

use Illuminate\Support\Facades\Session;

class CaptchaService
{
    /**
     * Gera um novo captcha matemático
     *
     * @return array
     */
    public static function generate()
    {
        $num1 = rand(1, 10);
        $num2 = rand(1, 10);
        $result = $num1 + $num2;
        
        Session::put('captcha_result', $result);
        
        return [
            'question' => "$num1 + $num2 = ?",
            'num1' => $num1,
            'num2' => $num2,
            'result' => $result
        ];
    }
    
    /**
     * Valida a resposta do captcha
     *
     * @param int $userAnswer
     * @return bool
     */
    public static function validate($userAnswer)
    {
        $correctAnswer = Session::get('captcha_result');
        
        if ($correctAnswer === null) {
            return false;
        }
        
        return (int) $userAnswer === (int) $correctAnswer;
    }
    
    /**
     * Limpa o captcha da sessão
     *
     * @return void
     */
    public static function clear()
    {
        Session::forget('captcha_result');
    }
    
    /**
     * Gera um captcha e retorna como resposta JSON
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public static function generateJson()
    {
        $captcha = self::generate();
        
        return response()->json([
            'question' => $captcha['question'],
            'num1' => $captcha['num1'],
            'num2' => $captcha['num2']
        ]);
    }
}