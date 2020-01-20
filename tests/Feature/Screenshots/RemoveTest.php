<?php

namespace Tests\Feature\Screenshots;

use App\Models\Screenshot;
use App\User;
use Illuminate\Support\Facades\Storage;
use Tests\Facades\ScreenshotFactory;
use Tests\Facades\UserFactory;
use Tests\TestCase;

/**
 * Class RemoveTest
 */
class RemoveTest extends TestCase
{
    private const URI = '/v1/screenshots/remove';

    /**
     * @var User
     */
    private $admin;
    /**
     * @var Screenshot
     */
    private $screenshot;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = UserFactory::asAdmin()->withTokens()->create();

        Storage::fake();

        $this->screenshot = ScreenshotFactory::create();
    }

    public function test_remove(): void
    {
        $this->assertDatabaseHas('screenshots', $this->screenshot->toArray());

        $response = $this->actingAs($this->admin)->postJson(self::URI, ['id' => $this->screenshot->id]);

        $response->assertSuccess();
        $this->assertSoftDeleted('screenshots', ['id' => $this->screenshot->id]);
    }

    public function test_unauthorized(): void
    {
        $response = $this->postJson(self::URI);

        $response->assertUnauthorized();
    }

    public function test_without_params(): void
    {
        $response = $this->actingAs($this->admin)->postJson(self::URI);

        $response->assertValidationError();
    }
}
