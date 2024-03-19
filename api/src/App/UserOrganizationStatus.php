<?php

namespace App;

/**
 * A user's status in registering for an organization
 */
enum UserOrganizationStatus: string {
    // The user has no association with the organization
    case None = 'None';
    // The user has been invited to join the organization, but has not yet responded
    case Invited = 'Invited';
    // The user has applied to join the organization, but has not yet been accepted
    case Applied = 'Applied';
    // The user is a member of the organization, and can be assigned to activities
    case Member = 'Member';
    // The user is a manager of the organization, and can manage its members and activities
    case Manager = 'Manager';
}
