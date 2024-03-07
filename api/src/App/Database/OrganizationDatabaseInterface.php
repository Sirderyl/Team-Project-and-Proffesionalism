<?php

namespace App\Database;

interface OrganizationDatabaseInterface {
    public function get(string $id): \App\Organization;
}
