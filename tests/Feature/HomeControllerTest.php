<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Message;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;

class HomeControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    #[Test]
    public function it_displays_home_page_successfully()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertViewIs('home');
    }

    #[Test]
    public function it_generates_captcha_successfully()
    {
        $response = $this->get('/generate-captcha');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'question',
            'num1',
            'num2'
        ]);

        // Verifica se a sessão foi definida
        $this->assertNotNull(session('captcha_result'));
    }

    #[Test]
    public function it_sends_message_with_valid_data_and_captcha()
    {
        // Primeiro gera um captcha
        $this->get('/generate-captcha');
        $captchaResult = session('captcha_result');

        $messageData = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'phone' => $this->faker->phoneNumber,
            'company' => $this->faker->company,
            'subject' => $this->faker->sentence,
            'message' => $this->faker->paragraph,
            'captcha_answer' => $captchaResult
        ];

        $response = $this->from('/generate-captcha')->post('/send-message', $messageData);

        $response->assertRedirect('/generate-captcha');
        $response->assertSessionHas('success', 'Mensagem enviada com sucesso! Entrarei em contato em breve.');

        // Verifica se a mensagem foi salva no banco
        $this->assertDatabaseHas('messages', [
            'name' => $messageData['name'],
            'email' => $messageData['email'],
            'subject' => $messageData['subject'],
            'message' => $messageData['message'],
            'is_read' => false
        ]);
    }

    #[Test]
    public function it_fails_to_send_message_with_invalid_captcha()
    {
        // Primeiro gera um captcha
        $this->get('/generate-captcha');

        $messageData = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'subject' => $this->faker->sentence,
            'message' => $this->faker->paragraph,
            'captcha_answer' => 999 // Resposta incorreta
        ];

        $response = $this->from('/generate-captcha')->post('/send-message', $messageData);

        $response->assertRedirect('/generate-captcha');
        $response->assertSessionHasErrors(['captcha_answer']);

        // Verifica se a mensagem NÃO foi salva no banco
        $this->assertDatabaseMissing('messages', [
            'name' => $messageData['name'],
            'email' => $messageData['email']
        ]);
    }

    #[Test]
    public function it_fails_to_send_message_without_captcha_session()
    {
        $messageData = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'subject' => $this->faker->sentence,
            'message' => $this->faker->paragraph,
            'captcha_answer' => 10
        ];

        $response = $this->post('/send-message', $messageData);

        $response->assertRedirect('/');
        $response->assertSessionHasErrors(['captcha_answer']);
    }

    #[Test]
    public function it_validates_required_fields()
    {
        $response = $this->post('/send-message', []);

        $response->assertRedirect('/');
        $response->assertSessionHasErrors([
            'name',
            'email',
            'subject',
            'message',
            'captcha_answer'
        ]);
    }

    #[Test]
    public function it_validates_email_format()
    {
        $this->get('/generate-captcha');
        $captchaAnswer = session('captcha_answer');

        $messageData = [
            'name' => $this->faker->name,
            'email' => 'invalid-email',
            'subject' => $this->faker->sentence,
            'message' => $this->faker->paragraph,
            'captcha_answer' => $captchaAnswer
        ];

        $response = $this->from('/generate-captcha')->post('/send-message', $messageData);

        $response->assertRedirect('/generate-captcha');
        $response->assertSessionHasErrors(['email']);
    }

    #[Test]
    public function it_validates_maximum_field_lengths()
    {
        $this->get('/generate-captcha');
        $captchaAnswer = session('captcha_answer');

        $messageData = [
            'name' => str_repeat('a', 256), // Excede 255 caracteres
            'email' => $this->faker->email,
            'subject' => str_repeat('b', 256), // Excede 255 caracteres
            'message' => $this->faker->paragraph,
            'captcha_answer' => $captchaAnswer
        ];

        $response = $this->from('/generate-captcha')->post('/send-message', $messageData);

        $response->assertRedirect('/generate-captcha');
        $response->assertSessionHasErrors(['name', 'subject']);
    }

    #[Test]
    public function it_sends_message_with_only_required_fields()
    {
        $this->get('/generate-captcha');
        $captchaResult = session('captcha_result');

        $messageData = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'subject' => $this->faker->sentence,
            'message' => $this->faker->paragraph,
            'captcha_answer' => $captchaResult
        ];

        $response = $this->from('/generate-captcha')->post('/send-message', $messageData);

        $response->assertRedirect('/generate-captcha');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('messages', [
            'name' => $messageData['name'],
            'email' => $messageData['email'],
            'subject' => $messageData['subject'],
            'message' => $messageData['message'],
            'phone' => null,
            'company' => null,
            'is_read' => false
        ]);
    }

    #[Test]
    public function it_clears_captcha_session_after_successful_submission()
    {
        $this->get('/generate-captcha');
        $captchaResult = session('captcha_result');

        $messageData = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'subject' => $this->faker->sentence,
            'message' => $this->faker->paragraph,
            'captcha_answer' => $captchaResult
        ];

        $this->from('/generate-captcha')->post('/send-message', $messageData);

        // Verifica se a sessão do captcha foi limpa
        $this->assertNull(session('captcha_result'));
    }

    #[Test]
    public function it_generates_different_captcha_questions()
    {
        $responses = [];
        
        // Gera múltiplos captchas para verificar variação
        for ($i = 0; $i < 10; $i++) {
            $response = $this->get('/generate-captcha');
            $data = $response->json();
            $responses[] = $data['question'];
        }

        // Verifica se há pelo menos alguma variação nas perguntas
        $uniqueQuestions = array_unique($responses);
        $this->assertGreaterThan(1, count($uniqueQuestions), 'Captcha should generate different questions');
    }

    #[Test]
    public function it_generates_captcha_with_correct_mathematical_operations()
    {
        $response = $this->get('/generate-captcha');
        $data = $response->json();

        // Verifica se a pergunta contém operadores matemáticos (apenas soma por enquanto)
        $this->assertMatchesRegularExpression('/\d+\s*\+\s*\d+\s*=\s*\?/', $data['question']);
        
        // Verifica se num1 e num2 são números
        $this->assertIsNumeric($data['num1']);
        $this->assertIsNumeric($data['num2']);
        
        // Verifica se a operação está correta
        $expectedResult = $data['num1'] + $data['num2'];
        $this->assertEquals($expectedResult, session('captcha_result'));
    }
}