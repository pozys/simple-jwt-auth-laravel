<?php

declare(strict_types=1);

namespace Tests\Feature\Api\v1;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class JWTAuthControllerTest extends TestCase
{
    use DatabaseTransactions;

    private User $user;
    private string $password = 'password';
    private array $userStructure = ['id', 'name', 'email'];

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['password' => Hash::make($this->password)]);
    }

    public function testRegister(): void
    {
        $response = $this->json('POST', route('api.v1.auth.register'), [
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'password' => fake()->password(8),
        ]);

        $response
            ->assertCreated()
            ->assertJsonStructure($this->userStructure);
    }

    public function testLogin(): void
    {
        $response = $this->json('POST', route('api.v1.auth.login'), [
            'email' => $this->user->email,
            'password' => $this->password
        ]);

        $response
            ->assertOk()
            ->assertJsonStructure(['access_token', 'token_type', 'expires_in']);
    }

    public function testProfile(): void
    {
        $response = $this
            ->withToken($this->getUserToken())
            ->json('GET', route('api.v1.auth.profile'));

        $response
            ->assertOk()
            ->assertJsonStructure($this->userStructure);
    }

    public function testLogout(): void
    {
        $response = $this
            ->withToken($this->getUserToken())
            ->json('POST', route('api.v1.auth.logout'));

        $response->assertOk();
    }

    public function testUnauthorized(): void
    {
        $response = $this->json('GET', route('api.v1.auth.profile'));

        $response->assertUnauthorized();
    }

    private function getUserToken(): string
    {
        return JWTAuth::fromUser($this->user);
    }
}
