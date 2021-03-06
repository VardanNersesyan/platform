<?php

declare(strict_types=1);

namespace Orchid\Tests\Unit;

use Orchid\Platform\Dashboard;
use Orchid\Tests\TestUnitCase;
use Orchid\Platform\Models\Role;
use Orchid\Platform\Models\User;

/**
 * Class PermissionTest.
 */
class PermissionTest extends TestUnitCase
{
    /**
     * Verify permissions.
     */
    public function testIsPermission()
    {
        $user = $this->createUser();

        // User permissions
        $this->assertEquals($user->hasAccess('access.to.public.data'), true);
        $this->assertEquals($user->hasAccess('access.to.secret.data'), false);
        $this->assertEquals($user->hasAccess('access.roles.to.public.data'), false);

        $role = $this->createRole();
        $user->addRole($role);

        // User of role
        $this->assertEquals($user->getRoles()->count(), 1);
        $this->assertEquals($role->getUsers()->count(), 1);
        $this->assertEquals($user->inRole('admin'), true);

        // Role permissions
        $this->assertEquals($user->hasAccess('access.roles.to.public.data', false), true);
    }

    /**
     * @return $this|\Illuminate\Database\Eloquent\Model
     */
    private function createUser()
    {
        return User::firstOrCreate([
            'email' => 'test@test.com',
        ], [
            'name'        => 'test',
            'email'       => 'test@test.com',
            'password'    => 'password',
            'permissions' => [
                'access.to.public.data' => 1,
                'access.to.secret.data' => 0,
            ],
        ]);
    }

    /**
     * @return $this|\Illuminate\Database\Eloquent\Model
     */
    private function createRole()
    {
        return Role::firstOrCreate([
            'slug' => 'admin',
        ], [
            'slug'        => 'admin',
            'name'        => 'admin',
            'permissions' => [
                'access.roles.to.public.data' => 1,
                'access.roles.to.secret.data' => 0,
            ],
        ]);
    }

    /**
     * Dashboard registered permission.
     */
    public function testIsRegisteredPermission()
    {
        $dashboard = new Dashboard();

        $dashboard->registerPermissions([
            'Test' => [
                [
                    'slug'        => 'test',
                    'description' => 'Test Description',
                ],
            ],
        ]);

        $this->assertEquals($dashboard->getPermission()->count(), 1);
    }

    /*
        public function test_it_replase_permission()
        {
            $user = $this->createUser();

            $user->replaceRoles([]);
            //
        }

        public function test_it_delete_user()
        {
            $user = $this->createUser();
            $user->delete();
        }
    */
}
