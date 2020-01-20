<?php

namespace Tests\Feature\Users;

use Tests\Facades\UserFactory;
use App\User;
use Tests\TestCase;

/**
 * Class ListTest
 */
class ListTest extends TestCase
{
    private const URI = 'v1/users/list';

    private const USERS_AMOUNT = 10;

    /**
     * @var User
     */
    private $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = UserFactory::asAdmin()->withTokens()->create();

        UserFactory::createMany(self::USERS_AMOUNT);
    }

    public function test_list(): void
    {
        $response = $this->actingAs($this->admin)->getJson(self::URI);

        $response->assertOk();
        $this->assertEquals(count($response->json()), User::count());

        //TODO change later
    }

    public function test_unauthorized(): void
    {
        $response = $this->getJson(self::URI);

        $response->assertUnauthorized();
    }
}
