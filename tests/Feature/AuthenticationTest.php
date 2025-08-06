<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Authentication', function () {
    it('redirects unauthenticated users to login page', function () {
        $response = $this->get('/pos');
        
        $response->assertRedirect('/login');
    });

    it('allows authenticated users to access POS', function () {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'job_role' => 'cashier'
        ]);

        $response = $this->actingAs($user)->get('/pos');
        
        $response->assertStatus(200);
        $response->assertViewIs('pos.index');
    });

    it('shows login page for unauthenticated users', function () {
        $response = $this->get('/login');
        
        $response->assertStatus(200);
        $response->assertViewIs('auth.login');
    });

    it('authenticates valid credentials', function () {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'job_role' => 'cashier'
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password'
        ]);

        $response->assertRedirect('/pos');
        $this->assertAuthenticated();
    });

    it('rejects invalid credentials', function () {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'job_role' => 'cashier'
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword'
        ]);

        $response->assertSessionHasErrors();
        $this->assertGuest();
    });

    it('logs out authenticated users', function () {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)->post('/logout');
        
        $response->assertRedirect('/login');
        $this->assertGuest();
    });

    it('provides authentication status via API', function () {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)->get('/test-auth');
        
        $response->assertStatus(200)
            ->assertJson([
                'authenticated' => true,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'job_role' => $user->job_role
                ]
            ]);
    });

    it('denies access to unauthenticated users via API', function () {
        $response = $this->get('/test-auth');
        
        $response->assertStatus(200)
            ->assertJson(['authenticated' => false]);
    });
}); 