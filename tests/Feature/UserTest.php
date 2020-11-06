<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\SetUp\ProjectFactory;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        $this->refreshApplicationWithLocale('en');
        parent::setUp();
    }

    /** @test */
    public function a_user_has_projects()
    {
        $user = User::factory()->create();
        $this->assertInstanceOf(Collection::class, $user->projects);
    }

    /** @test */
    public function a_user_has_all_projects()
    {
        //create John user
        $john = $this->signIn();
        //create a project and assign it to john
        (new ProjectFactory())->ownedBy($john)->create();
        //john should have 1 project
        $this->assertCount(1, $john->allProjects());
        //create Sally user
        $sally = User::factory()->create();
        $nick = User::factory()->create();
        //create a project by Sally and invite john
        $sallyProject = (new ProjectFactory())->ownedBy($sally)->create();
        $sallyProject->invite($nick);
        //john should have 2 projects in total, the one he created and the one he invited to
        $this->assertCount(1, $john->allProjects());
        $sallyProject->invite($john);
        $this->assertCount(2, $john->refresh()->allProjects());
    }
}
