<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Message;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class MessageModelTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_create_message_with_all_fields()
    {
        $messageData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '(11) 99999-9999',
            'company' => 'Test Company',
            'subject' => 'Test Subject',
            'message' => 'Test message content'
        ];

        $message = Message::create($messageData);

        $this->assertInstanceOf(Message::class, $message);
        $this->assertEquals($messageData['name'], $message->name);
        $this->assertEquals($messageData['email'], $message->email);
        $this->assertEquals($messageData['phone'], $message->phone);
        $this->assertEquals($messageData['subject'], $message->subject);
        $this->assertEquals($messageData['message'], $message->message);
    }

    #[Test]
    public function it_has_correct_fillable_attributes()
    {
        $message = new Message();
        $expected = [
            'name',
            'email',
            'subject',
            'message',
            'phone',
            'company',
            'is_read',
            'is_replied',
            'admin_reply',
            'replied_at',
        ];
        
        $this->assertEquals($expected, $message->getFillable());
    }

    #[Test]
    public function it_has_correct_casts()
    {
        $message = new Message();
        $casts = $message->getCasts();
        
        $this->assertEquals('boolean', $casts['is_read']);
        $this->assertEquals('boolean', $casts['is_replied']);
        $this->assertEquals('datetime', $casts['replied_at']);
    }

    #[Test]
    public function it_defaults_to_unread_and_unreplied()
    {
        $message = Message::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'subject' => 'Test Subject',
            'message' => 'Test message content',
            'is_read' => false,
            'is_replied' => false
        ]);

        $this->assertFalse($message->is_read);
        $this->assertFalse($message->is_replied);
        $this->assertNull($message->admin_reply);
        $this->assertNull($message->replied_at);
    }

    #[Test]
    public function scope_unread_returns_only_unread_messages()
    {
        // Criar mensagens lidas e não lidas
        Message::create([
            'name' => 'User 1',
            'email' => 'user1@example.com',
            'subject' => 'Subject 1',
            'message' => 'Message 1',
            'is_read' => false
        ]);

        Message::create([
            'name' => 'User 2',
            'email' => 'user2@example.com',
            'subject' => 'Subject 2',
            'message' => 'Message 2',
            'is_read' => true
        ]);

        Message::create([
            'name' => 'User 3',
            'email' => 'user3@example.com',
            'subject' => 'Subject 3',
            'message' => 'Message 3',
            'is_read' => false
        ]);

        $unreadMessages = Message::unread()->get();

        $this->assertCount(2, $unreadMessages);
        $this->assertTrue($unreadMessages->every(fn($message) => !$message->is_read));
    }

    #[Test]
    public function scope_unanswered_returns_only_unanswered_messages()
    {
        // Criar mensagens respondidas e não respondidas
        Message::create([
            'name' => 'User 1',
            'email' => 'user1@example.com',
            'subject' => 'Subject 1',
            'message' => 'Message 1',
            'is_replied' => false
        ]);

        Message::create([
            'name' => 'User 2',
            'email' => 'user2@example.com',
            'subject' => 'Subject 2',
            'message' => 'Message 2',
            'is_replied' => true,
            'admin_reply' => 'Admin response',
            'replied_at' => now()
        ]);

        Message::create([
            'name' => 'User 3',
            'email' => 'user3@example.com',
            'subject' => 'Subject 3',
            'message' => 'Message 3',
            'is_replied' => false
        ]);

        $unansweredMessages = Message::unanswered()->get();

        $this->assertCount(2, $unansweredMessages);
        $this->assertTrue($unansweredMessages->every(fn($message) => !$message->is_replied));
    }

    #[Test]
    public function mark_as_read_method_works_correctly()
    {
        $message = Message::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'subject' => 'Test Subject',
            'message' => 'Test message',
            'is_read' => false
        ]);

        $this->assertFalse($message->is_read);

        $message->markAsRead();

        $this->assertTrue($message->fresh()->is_read);
    }

    #[Test]
    public function mark_as_replied_method_works_correctly()
    {
        $message = Message::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'subject' => 'Test Subject',
            'message' => 'Test message',
            'is_replied' => false
        ]);

        $replyText = 'Thank you for your message!';

        $this->assertFalse($message->is_replied);
        $this->assertNull($message->admin_reply);
        $this->assertNull($message->replied_at);

        $message->markAsReplied($replyText);

        $freshMessage = $message->fresh();
        $this->assertTrue($freshMessage->is_replied);
        $this->assertEquals($replyText, $freshMessage->admin_reply);
        $this->assertNotNull($freshMessage->replied_at);
        $this->assertInstanceOf(\Carbon\Carbon::class, $freshMessage->replied_at);
    }

    #[Test]
    public function mark_as_replied_with_reply_text_works()
    {
        $message = Message::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'subject' => 'Test Subject',
            'message' => 'Test message',
            'is_replied' => false
        ]);

        $replyText = 'Thank you for your message!';
        $message->markAsReplied($replyText);

        $freshMessage = $message->fresh();
        $this->assertTrue($freshMessage->is_replied);
        $this->assertEquals($replyText, $freshMessage->admin_reply);
        $this->assertNotNull($freshMessage->replied_at);
    }

    #[Test]
    public function it_can_handle_optional_fields()
    {
        $message = Message::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'subject' => 'Test Subject',
            'message' => 'Test message'
            // phone, ip_address, user_agent são opcionais
        ]);

        $this->assertNull($message->phone);
        $this->assertNull($message->ip_address);
        $this->assertNull($message->user_agent);
    }

    #[Test]
    public function it_requires_essential_fields()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        // Tentar criar mensagem sem campos obrigatórios
        Message::create([
            'phone' => '(11) 99999-9999'
            // Faltam name, email, subject, message
        ]);
    }

    #[Test]
    public function it_can_combine_scopes()
    {
        // Criar mensagens com diferentes estados
        Message::create([
            'name' => 'User 1',
            'email' => 'user1@example.com',
            'subject' => 'Subject 1',
            'message' => 'Message 1',
            'is_read' => false,
            'is_replied' => false
        ]);

        Message::create([
            'name' => 'User 2',
            'email' => 'user2@example.com',
            'subject' => 'Subject 2',
            'message' => 'Message 2',
            'is_read' => true,
            'is_replied' => false
        ]);

        Message::create([
            'name' => 'User 3',
            'email' => 'user3@example.com',
            'subject' => 'Subject 3',
            'message' => 'Message 3',
            'is_read' => false,
            'is_replied' => true,
            'admin_reply' => 'Reply',
            'replied_at' => now()
        ]);

        // Buscar mensagens não lidas E não respondidas
        $unreadAndUnanswered = Message::unread()->unanswered()->get();

        $this->assertCount(1, $unreadAndUnanswered);
        $this->assertEquals('User 1', $unreadAndUnanswered->first()->name);
    }
}