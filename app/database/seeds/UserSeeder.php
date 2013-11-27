<?php

/**
 * Seeder for user
 * @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Michiel Vancoillie <michiel@okfn.be>
 */
class UserSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Eloquent::unguard();

        // Create 'everyone' user and group
        Sentry::getUserProvider()->create(array(
            'email'       => 'everyone',
            'password'    => 'everyone',
            'first_name'  => 'Jane',
            'last_name'   => 'Appleseed',
            'activated'   => 1,
        ));

        Sentry::getGroupProvider()->create(array(
            'name'        => 'everyone',
            'permissions' => array(
                'discovery.view' => 1,
                'dataset.view' => 1,
                'info.view' => 1,
                'definition.create' => 0,
                'definition.view' => 0,
                'definition.update' => 0,
                'definition.delete' => 0,
                'admin.dataset.view' => 0,
                'admin.dataset.create' => 0,
                'admin.dataset.update' => 0,
                'admin.dataset.delete' => 0,
                'admin.user.view' => 0,
                'admin.user.create' => 0,
                'admin.user.update' => 0,
                'admin.user.delete' => 0,
                'admin.group.view' => 0,
                'admin.group.create' => 0,
                'admin.group.update' => 0,
                'admin.group.delete' => 0,
            ),
        ));

        // Create admin user and group
        Sentry::getUserProvider()->create(array(
            'email'       => 'admin',
            'password'    => 'admin',
            'first_name'  => 'John',
            'last_name'   => 'Appleseed',
            'activated'   => 1,
        ));

        Sentry::getGroupProvider()->create(array(
            'name'        => 'superadmin'
        ));

        // Assign user permissions
        $everyoneUser  = Sentry::getUserProvider()->findByLogin('everyone');
        $everyoneGroup = Sentry::getGroupProvider()->findByName('everyone');
        $everyoneUser->addGroup($everyoneGroup);
        $this->command->info('Succesfully added user "everyone".');

        $adminUser  = Sentry::getUserProvider()->findByLogin('admin');
        $adminGroup = Sentry::getGroupProvider()->findByName('superadmin');
        $adminUser->addGroup($adminGroup);
        $this->command->info('Succesfully added user "admin".');

    }

}