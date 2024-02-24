-- Schema for the database
-- Expected to be run on an empty database

CREATE TABLE user (
    id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    email TEXT NOT NULL UNIQUE COLLATE NOCASE,
    password_hash TEXT NOT NULL,
    profile_picture BLOB
);

CREATE TABLE organization (
    id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    manager_id INTEGER NOT NULL REFERENCES user(id)
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

CREATE TABLE user_activity (
    user_id INTEGER NOT NULL REFERENCES user(id),
    activity_id INTEGER NOT NULL REFERENCES activity(id),
    rating INTEGER,
    -- TODO: Once recurring events are supported, we need to add a date field and add it to the primary key
    PRIMARY KEY (user_id, activity_id)
);
