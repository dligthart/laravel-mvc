<?php
declare(strict_types=1);

namespace Tychovbh\Tests\Mvc\Feature\Commands;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Tychovbh\Mvc\Role;
use Tychovbh\Mvc\User;
use Tychovbh\Tests\Mvc\TestCase;

class MvcUpdateTest extends TestCase
{
    /**
     * @test
     */
    public function itCanUpdateMvc()
    {
        $user = factory(User::class)->make();
        $role = factory(Role::class)->create();
        $this->artisan('mvc-user:create', [
            '--email' => $user->email,
            '--password' => $user->password,
            '--name' => $user->name,
            '--role' => $role->label,
            '--admin' => $user->is_admin
        ]);

        $this->assertDatabaseHas('users', [
            'email' => $user->email,
            'name' => $user->name,
            'is_admin' => $user->is_admin
        ]);

        $this->assertDatabaseHas('user_roles', [
            'user_id' => 1,
            'role_id' => $role->id,
        ]);
    }
}
