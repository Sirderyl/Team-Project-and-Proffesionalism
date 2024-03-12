<?php

namespace App\Debug;

/**
 * Debug functions for organizations
 */
class DebugOrganization {
    public static function createDummyOrganization(\Faker\Generator $faker, int $adminId): \App\Organization {
        $organisation = new \App\Organization();
        $organisation->name = $faker->unique()->company();
        $organisation->adminId = $adminId;

        return $organisation;
    }
}
