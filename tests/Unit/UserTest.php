<?php

namespace Tests\Unit;

//use App\Models\User;
//use Illuminate\Database\Eloquent\Collection;
//use Illuminate\Foundation\Testing\RefreshDatabase;
//use PHPUnit\Framework\TestCase;
use Tests\TestCase;

class UserTest extends TestCase
{
    protected function setUp(): void
    {
        $this->refreshApplicationWithLocale('en');
        parent::setUp();
    }

    /** @test */
    public function basic_test()
    {
        $this->assertTrue(true);
    }
}
