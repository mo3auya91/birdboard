<?php

namespace Tests\Unit;

use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\TestCase;

class ProjectTest extends TestCase
{
    /**
     * NOTE
     * in unit test there is no access to database and faker
     * if test needs to deal with database or faler, then move it to feature test
     */
}
