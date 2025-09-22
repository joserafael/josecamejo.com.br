<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;

class MessageFormValidationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private function getValidMessageData(): array
    {
        // Gera um captcha válido
        $this->get('/generate-captcha');
        $captchaAnswer = session('captcha_result');

        return [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'phone' => $this->faker->phoneNumber,
            'company' => $this->faker->company,
            'subject' => $this->faker->sentence,
            'message' => $this->faker->paragraph,
            'captcha_answer' => $captchaAnswer
        ];
    }

    #[Test]
    public function it_requires_name_field()
    {
        $data = $this->getValidMessageData();
        unset($data['name']);

        $response = $this->from('/generate-captcha')->post('/send-message', $data);

        $response->assertRedirect('/generate-captcha');
        $response->assertSessionHasErrors(['name']);
    }

    #[Test]
    public function it_requires_email_field()
    {
        $data = $this->getValidMessageData();
        unset($data['email']);

        $response = $this->from('/generate-captcha')->post('/send-message', $data);

        $response->assertRedirect('/generate-captcha');
        $response->assertSessionHasErrors(['email']);
    }

    #[Test]
    public function it_requires_subject_field()
    {
        $data = $this->getValidMessageData();
        unset($data['subject']);

        $response = $this->from('/generate-captcha')->post('/send-message', $data);

        $response->assertRedirect('/generate-captcha');
        $response->assertSessionHasErrors(['subject']);
    }

    #[Test]
    public function it_requires_message_field()
    {
        $data = $this->getValidMessageData();
        unset($data['message']);

        $response = $this->from('/generate-captcha')->post('/send-message', $data);

        $response->assertRedirect('/generate-captcha');
        $response->assertSessionHasErrors(['message']);
    }

    #[Test]
    public function it_requires_captcha_answer_field()
    {
        $data = $this->getValidMessageData();
        unset($data['captcha_answer']);

        $response = $this->from('/generate-captcha')->post('/send-message', $data);

        $response->assertRedirect('/generate-captcha');
        $response->assertSessionHasErrors(['captcha_answer']);
    }

    #[Test]
    public function it_validates_email_format()
    {
        $invalidEmails = [
            'invalid-email',
            'test@',
            '@example.com',
            'test.example.com',
            'test@.com',
            'test@example.',
            ''
        ];

        foreach ($invalidEmails as $invalidEmail) {
            $data = $this->getValidMessageData();
            $data['email'] = $invalidEmail;

            $response = $this->from('/generate-captcha')->post('/send-message', $data);

            $response->assertRedirect('/generate-captcha');
            $response->assertSessionHasErrors(['email'], 
                "Email '{$invalidEmail}' should be invalid");
        }
    }

    #[Test]
    public function it_accepts_valid_email_formats()
    {
        $validEmails = [
            'test@example.com',
            'user.name@domain.co.uk',
            'test+tag@example.org',
            'user123@test-domain.com'
        ];

        foreach ($validEmails as $validEmail) {
            $data = $this->getValidMessageData();
            $data['email'] = $validEmail;

            $response = $this->from('/generate-captcha')->post('/send-message', $data);

            $response->assertRedirect('/generate-captcha');
            $response->assertSessionHas('success');
        }
    }

    #[Test]
    public function it_validates_name_max_length()
    {
        $data = $this->getValidMessageData();
        $data['name'] = str_repeat('a', 256); // Excede 255 caracteres

        $response = $this->from('/generate-captcha')->post('/send-message', $data);

        $response->assertRedirect('/generate-captcha');
        $response->assertSessionHasErrors(['name']);
    }

    #[Test]
    public function it_validates_subject_max_length()
    {
        $data = $this->getValidMessageData();
        $data['subject'] = str_repeat('a', 256); // Excede 255 caracteres

        $response = $this->from('/generate-captcha')->post('/send-message', $data);

        $response->assertRedirect('/generate-captcha');
        $response->assertSessionHasErrors(['subject']);
    }

    #[Test]
    public function it_validates_phone_max_length()
    {
        $data = $this->getValidMessageData();
        $data['phone'] = str_repeat('1', 21); // Excede 20 caracteres

        $response = $this->from('/generate-captcha')->post('/send-message', $data);

        $response->assertRedirect('/generate-captcha');
        $response->assertSessionHasErrors(['phone']);
    }

    #[Test]
    public function it_validates_company_max_length()
    {
        $data = $this->getValidMessageData();
        $data['company'] = str_repeat('a', 256); // Excede 255 caracteres

        $response = $this->from('/generate-captcha')->post('/send-message', $data);

        $response->assertRedirect('/generate-captcha');
        $response->assertSessionHasErrors(['company']);
    }

    #[Test]
    public function it_accepts_optional_fields_as_null()
    {
        $data = $this->getValidMessageData();
        $data['phone'] = null;
        $data['company'] = null;

        $response = $this->from('/generate-captcha')->post('/send-message', $data);

        $response->assertRedirect('/generate-captcha');
        $response->assertSessionHas('success');
    }

    #[Test]
    public function it_accepts_optional_fields_as_empty_string()
    {
        $data = $this->getValidMessageData();
        $data['phone'] = '';
        $data['company'] = '';

        $response = $this->from('/generate-captcha')->post('/send-message', $data);

        $response->assertRedirect('/generate-captcha');
        $response->assertSessionHas('success');
    }

    #[Test]
    public function it_validates_captcha_answer_is_numeric()
    {
        $this->get('/generate-captcha');
        
        $data = $this->getValidMessageData();
        $data['captcha_answer'] = 'not-a-number';

        $response = $this->from('/generate-captcha')->post('/send-message', $data);

        $response->assertRedirect('/generate-captcha');
        $response->assertSessionHasErrors(['captcha_answer']);
    }

    #[Test]
    public function it_validates_captcha_answer_matches_session()
    {
        $this->get('/generate-captcha');
        $correctAnswer = session('captcha_result');
        
        $data = $this->getValidMessageData();
        $data['captcha_answer'] = $correctAnswer + 1; // Resposta incorreta

        $response = $this->from('/generate-captcha')->post('/send-message', $data);

        $response->assertRedirect('/generate-captcha');
        $response->assertSessionHasErrors(['captcha_answer']);
    }

    #[Test]
    public function it_trims_whitespace_from_fields()
    {
        $data = $this->getValidMessageData();
        $data['name'] = '  John Doe  ';
        $data['email'] = '  test@example.com  ';
        $data['subject'] = '  Test Subject  ';

        $response = $this->from('/generate-captcha')->post('/send-message', $data);

        $response->assertRedirect('/generate-captcha');
        $response->assertSessionHas('success');

        // Verifica se os dados foram salvos sem espaços extras
        $this->assertDatabaseHas('messages', [
            'name' => 'John Doe',
            'email' => 'test@example.com',
            'subject' => 'Test Subject'
        ]);
    }

    #[Test]
    public function it_handles_special_characters_in_fields()
    {
        $data = $this->getValidMessageData();
        $data['name'] = 'José da Silva';
        $data['subject'] = 'Assunto com acentos: ção, ã, é';
        $data['message'] = 'Mensagem com caracteres especiais: @#$%&*()';

        $response = $this->from('/generate-captcha')->post('/send-message', $data);

        $response->assertRedirect('/generate-captcha');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('messages', [
            'name' => 'José da Silva',
            'subject' => 'Assunto com acentos: ção, ã, é',
            'message' => 'Mensagem com caracteres especiais: @#$%&*()'
        ]);
    }

    #[Test]
    public function it_prevents_xss_attacks_in_form_fields()
    {
        $data = $this->getValidMessageData();
        $data['name'] = '<script>alert("xss")</script>';
        $data['subject'] = '<img src="x" onerror="alert(1)">';
        $data['message'] = '<iframe src="javascript:alert(1)"></iframe>';

        $response = $this->from('/generate-captcha')->post('/send-message', $data);

        $response->assertRedirect('/generate-captcha');
        $response->assertSessionHas('success');

        // Verifica se os dados foram salvos (Laravel automaticamente escapa)
        $this->assertDatabaseHas('messages', [
            'name' => '<script>alert("xss")</script>',
            'subject' => '<img src="x" onerror="alert(1)">',
            'message' => '<iframe src="javascript:alert(1)"></iframe>'
        ]);
    }
}