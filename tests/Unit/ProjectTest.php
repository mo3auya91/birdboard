<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\TestCase;
//use Tests\TestCase;

class ProjectTest extends TestCase
{
    use RefreshDatabase;
    /**
     * NOTE
     * in unit test there is no access to database and faker
     * if test needs to deal with database or faler, then move it to feature test
     */

    /** @test */
    public function basic_test()
    {
        $this->assertTrue(true);
    }
}
