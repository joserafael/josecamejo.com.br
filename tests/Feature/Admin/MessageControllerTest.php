<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\User;
use App\Models\Message;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class MessageControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $regularUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::factory()->create(['is_admin' => true]);
        $this->regularUser = User::factory()->create(['is_admin' => false]);
    }

    #[Test]
    public function admin_can_view_messages_index()
    {
        $this->actingAs($this->adminUser);

        Message::factory()->count(3)->create();

        $response = $this->get(route('admin.messages.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.messages.index');
        $response->assertViewHas('messages');
        $response->assertViewHas('pageTitle', 'Gerenciar Mensagens');
    }

    #[Test]
    public function non_admin_cannot_access_messages_index()
    {
        $this->actingAs($this->regularUser);

        $response = $this->get(route('admin.messages.index'));

        $response->assertStatus(403);
    }

    #[Test]
    public function guest_cannot_access_messages_index()
    {
        $response = $this->get(route('admin.messages.index'));

        $response->assertRedirect(route('login'));
    }

    #[Test]
    public function admin_can_filter_messages_by_status()
    {
        $this->actingAs($this->adminUser);

        // Criar mensagens com diferentes status
        Message::factory()->create(['is_read' => false, 'is_replied' => false]);
        Message::factory()->create(['is_read' => true, 'is_replied' => false]);
        Message::factory()->create(['is_read' => true, 'is_replied' => true]);

        // Filtrar por não lidas
        $response = $this->get(route('admin.messages.index', ['status' => 'unread']));
        $response->assertStatus(200);

        // Filtrar por não respondidas
        $response = $this->get(route('admin.messages.index', ['status' => 'unanswered']));
        $response->assertStatus(200);

        // Filtrar por respondidas
        $response = $this->get(route('admin.messages.index', ['status' => 'replied']));
        $response->assertStatus(200);
    }

    #[Test]
    public function admin_can_search_messages()
    {
        $this->actingAs($this->adminUser);

        Message::factory()->create([
            'name' => 'João Silva',
            'email' => 'joao@example.com',
            'subject' => 'Contato sobre projeto',
            'message' => 'Gostaria de saber mais sobre seus serviços'
        ]);

        Message::factory()->create([
            'name' => 'Maria Santos',
            'email' => 'maria@example.com',
            'subject' => 'Orçamento',
            'message' => 'Preciso de um orçamento'
        ]);

        // Buscar por nome
        $response = $this->get(route('admin.messages.index', ['search' => 'João']));
        $response->assertStatus(200);

        // Buscar por email
        $response = $this->get(route('admin.messages.index', ['search' => 'maria@example.com']));
        $response->assertStatus(200);

        // Buscar por assunto
        $response = $this->get(route('admin.messages.index', ['search' => 'projeto']));
        $response->assertStatus(200);
    }

    #[Test]
    public function admin_can_view_create_message_form()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get(route('admin.messages.create'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.messages.create');
        $response->assertViewHas('pageTitle', 'Nova Mensagem');
    }

    #[Test]
    public function admin_can_create_new_message()
    {
        $this->actingAs($this->adminUser);

        $messageData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'subject' => 'Test Subject',
            'message' => 'This is a test message',
            'phone' => '(11) 99999-9999',
            'company' => 'Test Company'
        ];

        $response = $this->post(route('admin.messages.store'), $messageData);

        $response->assertRedirect(route('admin.messages.index'));
        $response->assertSessionHas('success', __('messages.success.message_created'));

        $this->assertDatabaseHas('messages', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'subject' => 'Test Subject',
            'message' => 'This is a test message'
        ]);
    }

    #[Test]
    public function admin_cannot_create_message_with_invalid_data()
    {
        $this->actingAs($this->adminUser);

        $response = $this->post(route('admin.messages.store'), [
            'name' => '', // Required field empty
            'email' => 'invalid-email', // Invalid email
            'subject' => '',
            'message' => ''
        ]);

        $response->assertSessionHasErrors(['name', 'email', 'subject', 'message']);
    }

    #[Test]
    public function admin_can_view_message_details()
    {
        $this->actingAs($this->adminUser);

        $message = Message::factory()->create(['is_read' => false]);

        $response = $this->get(route('admin.messages.show', $message));

        $response->assertStatus(200);
        $response->assertViewIs('admin.messages.show');
        $response->assertViewHas('message', $message);

        // Verificar se a mensagem foi marcada como lida
        $this->assertTrue($message->fresh()->is_read);
    }

    #[Test]
    public function admin_can_view_edit_message_form()
    {
        $this->actingAs($this->adminUser);

        $message = Message::factory()->create();

        $response = $this->get(route('admin.messages.edit', $message));

        $response->assertStatus(200);
        $response->assertViewIs('admin.messages.edit');
        $response->assertViewHas('message', $message);
    }

    #[Test]
    public function admin_can_update_message()
    {
        $this->actingAs($this->adminUser);

        $message = Message::factory()->create();

        $updateData = [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'subject' => 'Updated Subject',
            'message' => 'Updated message content',
            'phone' => '(11) 88888-8888',
            'company' => 'Updated Company',
            'is_read' => true,
            'is_replied' => true
        ];

        $response = $this->put(route('admin.messages.update', $message), $updateData);

        $response->assertRedirect(route('admin.messages.index'));
        $response->assertSessionHas('success', __('messages.success.message_updated'));

        $this->assertDatabaseHas('messages', [
            'id' => $message->id,
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'subject' => 'Updated Subject',
            'message' => 'Updated message content'
        ]);
    }

    #[Test]
    public function admin_can_delete_message()
    {
        $this->actingAs($this->adminUser);

        $message = Message::factory()->create();

        $response = $this->delete(route('admin.messages.destroy', $message));

        $response->assertRedirect(route('admin.messages.index'));
        $response->assertSessionHas('success', __('messages.success.message_deleted'));

        $this->assertDatabaseMissing('messages', ['id' => $message->id]);
    }

    #[Test]
    public function admin_can_reply_to_message()
    {
        $this->actingAs($this->adminUser);

        $message = Message::factory()->create(['is_replied' => false]);

        $replyData = [
            'reply' => 'Thank you for your message. We will get back to you soon.'
        ];

        $response = $this->post(route('admin.messages.reply', $message), $replyData);

        $response->assertRedirect(route('admin.messages.show', $message));
        $response->assertSessionHas('success', __('messages.success.reply_sent'));

        $message->refresh();
        $this->assertTrue($message->is_replied);
        $this->assertEquals($replyData['reply'], $message->admin_reply);
        $this->assertNotNull($message->replied_at);
    }

    #[Test]
    public function admin_cannot_reply_with_empty_message()
    {
        $this->actingAs($this->adminUser);

        $message = Message::factory()->create();

        $response = $this->post(route('admin.messages.reply', $message), [
            'reply' => ''
        ]);

        $response->assertSessionHasErrors(['reply']);
    }

    #[Test]
    public function admin_can_mark_message_as_read_via_ajax()
    {
        $this->actingAs($this->adminUser);

        $message = Message::factory()->create(['is_read' => false]);

        $response = $this->post(route('admin.messages.mark-as-read', $message));

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertTrue($message->fresh()->is_read);
    }

    #[Test]
    public function admin_can_toggle_read_status_via_ajax()
    {
        $this->actingAs($this->adminUser);

        $message = Message::factory()->create(['is_read' => false]);

        // Toggle para lida
        $response = $this->post(route('admin.messages.toggle-read', $message));

        $response->assertStatus(200);
        $response->assertJson(['success' => true, 'is_read' => true]);

        $this->assertTrue($message->fresh()->is_read);

        // Toggle para não lida
        $response = $this->post(route('admin.messages.toggle-read', $message));

        $response->assertStatus(200);
        $response->assertJson(['success' => true, 'is_read' => false]);

        $this->assertFalse($message->fresh()->is_read);
    }

    #[Test]
    public function non_admin_cannot_access_any_message_routes()
    {
        $this->actingAs($this->regularUser);

        $message = Message::factory()->create();

        // Test all routes
        $this->get(route('admin.messages.create'))->assertStatus(403);
        $this->post(route('admin.messages.store'), [])->assertStatus(403);
        $this->get(route('admin.messages.show', $message))->assertStatus(403);
        $this->get(route('admin.messages.edit', $message))->assertStatus(403);
        $this->put(route('admin.messages.update', $message), [])->assertStatus(403);
        $this->delete(route('admin.messages.destroy', $message))->assertStatus(403);
        $this->post(route('admin.messages.reply', $message), [])->assertStatus(403);
        $this->post(route('admin.messages.mark-as-read', $message))->assertStatus(403);
        $this->post(route('admin.messages.toggle-read', $message))->assertStatus(403);
    }

    #[Test]
    public function messages_are_paginated_correctly()
    {
        $this->actingAs($this->adminUser);

        // Criar mais mensagens do que o limite de paginação (15)
        Message::factory()->count(20)->create();

        $response = $this->get(route('admin.messages.index'));

        $response->assertStatus(200);
        $response->assertViewHas('messages');
        
        $messages = $response->viewData('messages');
        $this->assertEquals(15, $messages->perPage());
        $this->assertEquals(20, $messages->total());
    }

    #[Test]
    public function messages_are_ordered_by_created_at_desc()
    {
        $this->actingAs($this->adminUser);

        $oldMessage = Message::factory()->create(['created_at' => now()->subDays(2)]);
        $newMessage = Message::factory()->create(['created_at' => now()]);
        $middleMessage = Message::factory()->create(['created_at' => now()->subDay()]);

        $response = $this->get(route('admin.messages.index'));

        $response->assertStatus(200);
        $messages = $response->viewData('messages');
        
        // Verificar se estão ordenadas por data de criação (mais recente primeiro)
        $this->assertEquals($newMessage->id, $messages->first()->id);
        $this->assertEquals($oldMessage->id, $messages->last()->id);
    }
}