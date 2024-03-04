-- Schema for the database
-- Expected to be run on an empty database

CREATE TABLE organization (
    id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    admin_id INTEGER NOT NULL REFERENCES user(id)
);

CREATE TABLE activity (
    id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    short_description TEXT NOT NULL,
    long_description TEXT NOT NULL,
    preview_picture BLOB,
    -- TODO: We need a way to support recurring events
    start_time DATETIME NOT NULL,
    end_time DATETIME NOT NULL,
    needed_volunteers INTEGER NOT NULL
);

CREATE TABLE user (
    id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    email TEXT NOT NULL UNIQUE COLLATE NOCASE,
    password_hash TEXT NOT NULL,
    -- In E.164 format
    phone_number TEXT NOT NULL UNIQUE,
    profile_picture BLOB
);

CREATE TABLE user_availability (
    user_id INTEGER NOT NULL REFERENCES user(id),
    day_of_week TEXT NOT NULL,
    start_hour INTEGER NOT NULL,
    end_hour INTEGER NOT NULL,

    CHECK (day_of_week IN ('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')),
    CHECK (start_hour >= 0 AND start_hour < 24),
    CHECK (end_hour > 0 AND end_hour <= 24),
    CHECK (start_hour < end_hour),
    PRIMARY KEY (user_id, day_of_week)
);

CREATE TABLE user_organization (
    user_id INTEGER NOT NULL REFERENCES user(id),
    organization_id INTEGER NOT NULL REFERENCES organization(id),

    status TEXT NOT NULL,

    PRIMARY KEY (user_id, organization_id),
    CHECK (status IN ('Invited', 'Applied', 'Member', 'Manager'))
);

CREATE TABLE user_activity (
    user_id INTEGER NOT NULL REFERENCES user(id),
    activity_id INTEGER NOT NULL REFERENCES activity(id),
    rating INTEGER,
    -- TODO: Once recurring events are supported, we need to add a date field and add it to the primary key
    PRIMARY KEY (user_id, activity_id)
);
