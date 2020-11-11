<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class LoginTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * @test
     * @throws \Throwable
     */
    public function a_registered_user_can_login()
    {
        $this->browse(function ($first) {
            $first->loginAs(User::find(1))
                ->visit('/dashboard');
        });
    }
}
