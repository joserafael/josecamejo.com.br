<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Controllers\HomeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use PHPUnit\Framework\Attributes\Test;

class CaptchaTest extends TestCase
{
    #[Test]
    public function it_generates_valid_mathematical_operations()
    {
        $response = $this->get('/generate-captcha');
        $data = $response->json();
        
        $this->assertArrayHasKey('question', $data);
        $this->assertArrayHasKey('num1', $data);
        $this->assertArrayHasKey('num2', $data);
        $this->assertIsString($data['question']);
        $this->assertIsNumeric($data['num1']);
        $this->assertIsNumeric($data['num2']);
        $this->assertNotNull(session('captcha_result'));
    }

    #[Test]
    public function it_generates_addition_operations_correctly()
    {
        $response = $this->get('/generate-captcha');
        $data = $response->json();
        
        // Verifica se contém operação de soma
        $this->assertStringContainsString('+', $data['question']);
        
        // Extrai os números da operação
        preg_match('/(\d+)\s*\+\s*(\d+)\s*=\s*\?/', $data['question'], $matches);
        $this->assertCount(3, $matches);
        
        $num1 = (int)$matches[1];
        $num2 = (int)$matches[2];
        $expectedAnswer = $num1 + $num2;
        
        // Verifica se o resultado está correto na sessão
        $this->assertEquals($expectedAnswer, session('captcha_result'));
        
        // Verifica se os números retornados estão corretos
        $this->assertEquals($num1, $data['num1']);
        $this->assertEquals($num2, $data['num2']);
    }

    #[Test]
    public function it_generates_only_addition_operations()
    {
        // Verifica que apenas operações de soma são geradas
        for ($i = 0; $i < 5; $i++) {
            $response = $this->get('/generate-captcha');
            $data = $response->json();
            
            // Verifica se contém apenas operação de soma
            $this->assertStringContainsString('+', $data['question']);
            $this->assertStringNotContainsString('-', $data['question']);
            $this->assertStringNotContainsString('*', $data['question']);
            
            // Verifica se a operação está correta
            preg_match('/(\d+)\s*\+\s*(\d+)\s*=\s*\?/', $data['question'], $matches);
            $this->assertCount(3, $matches);
            
            $num1 = (int)$matches[1];
            $num2 = (int)$matches[2];
            $expectedResult = $num1 + $num2;
            
            $this->assertEquals($expectedResult, session('captcha_result'));
        }
    }

    #[Test]
    public function it_generates_numbers_within_expected_range()
    {
        for ($i = 0; $i < 5; $i++) {
            $response = $this->get('/generate-captcha');
            $data = $response->json();
            
            // Verifica se os números estão no range esperado (1-10)
            $this->assertGreaterThanOrEqual(1, $data['num1'], 'num1 should be at least 1');
            $this->assertLessThanOrEqual(10, $data['num1'], 'num1 should be at most 10');
            $this->assertGreaterThanOrEqual(1, $data['num2'], 'num2 should be at least 1');
            $this->assertLessThanOrEqual(10, $data['num2'], 'num2 should be at most 10');
        }
    }



    #[Test]
    public function it_generates_different_numbers_randomly()
    {
        $questions = [];
        
        for ($i = 0; $i < 10; $i++) {
            $response = $this->get('/generate-captcha');
            $data = $response->json();
            $questions[] = $data['question'];
        }
        
        // Deve gerar diferentes perguntas (números diferentes)
        $uniqueQuestions = array_unique($questions);
        $this->assertGreaterThan(1, count($uniqueQuestions), 
            'Should generate different questions with different numbers');
    }

    #[Test]
    public function it_formats_question_correctly()
    {
        $response = $this->get('/generate-captcha');
        $data = $response->json();
        
        // Verifica se a pergunta termina com " = ?"
        $this->assertStringEndsWith(' = ?', $data['question']);
        
        // Verifica se contém operador de soma
        $this->assertStringContainsString('+', $data['question']);
    }
}