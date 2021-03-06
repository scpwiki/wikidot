<?php
declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Wikijump\Models\User;

class SettingsTest extends TestCase
{
    private $settings;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $user = User::factory()->create();
        $this->settings = $user->settings()->firstOrNew();
    }

    /**
     * Test the retrieve($setting) method.
     * This will generally be implemented by a parent class via its get($setting) method.
     */
    public function testRetrieve()
    {
        $this->assertTrue($this->settings->retrieve('allow_pms'));

        $this->settings->modify(['allow_pms' => false]);
        $this->assertFalse($this->settings->retrieve('allow_pms'));
        $this->assertEquals(1, count($this->settings->settings));
        $this->assertTrue($this->settings->exists());
    }

    public function testModify()
    {
        $this->assertTrue($this->settings->retrieve('allow_pms'));

        $this->settings->modify(['allow_pms' => false]);

        $this->assertFalse($this->settings->retrieve('allow_pms'));
        $this->assertEquals(1, count($this->settings->settings));

        $this->settings->modify(['show_last_online_time' => false]);
        $this->settings->refresh();
        $this->assertEquals(2, count($this->settings->settings));

        $this->settings->modify(['show_last_online_time' => true]);
        $this->assertEquals(1, count($this->settings->settings));

        $this->settings->modify(['allow_pms' => true]);

        $this->expectException(ModelNotFoundException::class);
        $this->settings->refresh();
    }
}
