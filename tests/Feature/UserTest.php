<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use function PHPUnit\Framework\assertNotNull;

class UserTest extends TestCase
{
    public function testRegisterSuccess(): void
    {
        $this -> post('/api/users', [
            'username' => 'ariefrahman',
            'password'=> 'rahasia',
            'name' => 'Arief Rahman',
        ])->assertStatus(201)
            ->assertJson([
                "data"=>[
                    'username' => 'ariefrahman',
                    'name'=> 'Arief Rahman',
                    ]
            ]);
    }

    public function testRegisterFailure(): void
    {
        $this -> post('/api/users', [
            'username' => '',
            'password'=> '',
            'name' => '',
        ])->assertStatus(400)
            ->assertJson([
                "errors"=>[
                    'username' => [
                        'The username field is required.'
                    ],
                    'password'=> [
                        'The password field is required.'
                    ],
                    'name'=> [
                        'The name field is required.'
                    ],
                ]
            ]);
    }

    public function testRegisterUsernameAlreadyExists(): void
    {
        $this -> testRegisterSuccess();
        $this -> post('/api/users', [
            'username' => 'ariefrahman',
            'password'=> 'rahasia',
            'name' => 'Arief Rahman',
        ])->assertStatus(400)
            ->assertJson([
                "errors"=>[
                    'username' => [
                        'Username already registered'
                    ]
                ]
            ]);
    }

    public function testLoginSuccess()
    {
        $this->seed([UserSeeder::class]);
        $this -> post('/api/users/login', [
            'username'=> 'test',
            'password'=> 'test',
        ])->assertStatus(200)
            ->assertJson([
                'data'=> [
                    'username'=> 'test',
                    'name' => 'test',
                ]
            ]);

        $user = User::where('username','test')->first();
        self::assertNotNull($user->token);
    }

    public function testLoginFailed(): void
    {
        $this->seed([UserSeeder::class]);
        $this -> post('/api/users/login', [
            'username'=> 'wrong',
            'password'=> 'test',
        ])->assertStatus(401)
            ->assertJson([
                'errors'=> [
                    'message' => [
                        'username or password wrong'
                    ]
                ]
            ]);

        $user = User::where('username','wrong')->first();
        self::assertNotNull($user->token);
    }
}
