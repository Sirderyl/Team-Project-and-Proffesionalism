<?php

namespace App;

/**
 * An organization in the system
 */
class Organization {
    /**
     * @param array{
     *  id: string,
     *  name: string,
     *  admin_id: string
     * } $row
     */
    public static function fromRow(array $row): Organization {
        $org = new Organization();
        $org->id = $row['id'];
        $org->name = $row['name'];
        $org->admin_id = $row['admin_id'];
        return $org;
    }

    public string $id;
    public string $name;
    public string $admin_id;
}
