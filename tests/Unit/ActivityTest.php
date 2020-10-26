<?php

namespace Tests\Unit;

//use PHPUnit\Framework\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\SetUp\ProjectFactory;
use Tests\TestCase;

class ActivityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        $this->refreshApplicationWithLocale('en');
        parent::setUp();
    }

    /** @test */
    public function it_has_a_user()
    {
        $user = $this->signIn();
        $project = (new ProjectFactory())->ownedBy($user)->create();
        $this->assertInstanceOf(User::class, $project->activities->first()->user);
        $this->assertEquals($user->id, $project->activities->first()->user_id);
    }
}
