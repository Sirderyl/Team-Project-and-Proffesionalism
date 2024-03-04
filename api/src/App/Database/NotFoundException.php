<?php

namespace App\Database;

/**
 * Exception thrown when a record is not found in the database
 * @author Kieran
 */
class NotFoundException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('The requested record was not found');
    }
}
