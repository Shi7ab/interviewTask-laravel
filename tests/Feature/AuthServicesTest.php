<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\AuthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AuthServicesTest extends TestCase
{
    use RefreshDatabase;

    protected AuthService $authService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->authService = new AuthService();
    }

    #[Test]
    public function it_can_register_a_user_successfully_and_hashes_password()
    {
        $registrationData = [
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
            'password' => 'secret123',
        ];

        $user = $this->authService->register($registrationData);

        // Assert: Instance verification
        $this->assertInstanceOf(User::class, $user);

        // Assert: Check database state (exclude password string since it's hashed)
        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
        ]);

        // Assert: Verify password was safely hashed
        $this->assertTrue(Hash::check('secret123', $user->password));
    }

    #[Test]
    public function it_returns_user_and_token_on_successful_login()
    {
        // Arrange: Seed a user into the DB
        $user = User::factory()->create([
            'email' => 'loginme@example.com',
            'password' => Hash::make('correct_password'),
        ]);

        $credentials = [
            'email' => 'loginme@example.com',
            'password' => 'correct_password',
        ];

        // Act: Invoke service
        $result = $this->authService->login($credentials);

        // Assert: Check structure array keys
        $this->assertIsArray($result);
        $this->assertArrayHasKey('user', $result);
        $this->assertArrayHasKey('token', $result);

        // Assert: Ensure identity matches
        $this->assertEquals($user->id, $result['user']->id);
        $this->assertNotEmpty($result['token']);
    }

    #[Test]
    public function it_returns_null_if_login_credentials_are_invalid()
    {
        // Arrange: Create user
        User::factory()->create([
            'email' => 'wrongpass@example.com',
            'password' => Hash::make('valid_password'),
        ]);

        $invalidCredentials = [
            'email' => 'wrongpass@example.com',
            'password' => 'bad_password_attempt',
        ];

        // Act
        $result = $this->authService->login($invalidCredentials);

        // Assert: Ensure service declines processing
        $this->assertNull($result);
    }

    #[Test]
    public function it_can_logout_authenticated_user()
    {
        $user = User::factory()->create();

        // Arrange: Authenticate using Sanctum/Auth gate
        $this->actingAs($user);

        // Act: Confirm they are logged in before running logout method
        $this->assertTrue(Auth::check());

        $this->authService->logout();

        // Assert: Ensure authentication layer cleared out
        $this->assertFalse(Auth::check());
    }
}
