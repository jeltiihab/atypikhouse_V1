<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    private $userData = [
        "firstName" => 'testFirstName',
        "lastName" => 'testLastName',
        "userRole" => "ROLE_LOC",
        "sexe" => "homme",
        "birthDate" => "20-01-1997",
        "email" => 'test@test.com',
        "phone" => "1234567891",
        "password" => "Example.2022",
        "password_confirmation" => "Example.2022",
    ];

    private $token = "";

    public function testSuccessfulRegistration()
    {
        $this->json('POST', '/v1/register', $this->userData, ['Accept' => 'application/json'])
            ->assertStatus(200);

        User::where('email', $this->userData['email'])->updated(['is_activated' => 1, 'email_verified_at' => date("Y-m-d H:i:s")]);
        $this->assertDatabaseHas('users', [
            'firstName' => 'testFirstName',
            'lastName' => 'testLastName',
            'email' => 'test@test.com'
        ]);
    }

    public function testNotAuthorized()
    {
        $this->json('GET', '/v1/greeting')
            ->assertStatus(401);
    }

    public function testSuccessfulLogin()
    {
        $credential = [
            'email' => $this->userData['email'],
            'password' => $this->userData['password'],
        ];
        $response = $this->json('POST', '/v1/login', $credential);
        $json = json_decode($response->getContent());
        $this->token = $json->access_token;
        $results = (array)$json;
        $this->assertArrayHasKey('access_token', $results);
        $this->json('GET', '/v1/greeting', ['Accept' => 'application/json', 'Authorization' => 'Bearer ' . $this->token])->assertStatus(200);
    }

}
