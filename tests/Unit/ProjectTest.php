<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

//use PHPUnit\Framework\TestCase;
use Tests\TestCase;
use Tests\SetUp\ProjectFactory;

class ProjectTest extends TestCase
{
    use RefreshDatabase;

    /**
     * NOTE
     * in unit test there is no access to database and faker
     * if test needs to deal with database or faler, then move it to feature test
     */

    protected function setUp(): void
    {
        $this->refreshApplicationWithLocale('en');
        parent::setUp();
    }

    /** @test */
    public function it_can_invite_a_user()
    {
        $project = (new ProjectFactory())->create();

        $project->invite($user = User::factory()->create());
        $this->assertTrue($project->members->contains($user));
    }
}
