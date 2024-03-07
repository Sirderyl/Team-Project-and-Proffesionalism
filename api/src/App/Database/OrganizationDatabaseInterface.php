<?php

namespace App\Database;

interface OrganizationDatabaseInterface {
    public function get(int $id): \App\Organization;

    public function create(\App\Organization $organization): void;
}
