<?php

namespace App;

/**
 * An organization in the system
 */
class Organization {
    /**
     * @param array{
     *  id: int,
     *  name: string,
     *  admin_id: int
     * } $row
     */
    public static function fromRow(array $row): Organization {
        $org = new Organization();
        $org->id = $row['id'];
        $org->name = $row['name'];
        $org->adminId = $row['admin_id'];
        return $org;
    }

    public int $id;
    public string $name;
    public int $adminId;
}
