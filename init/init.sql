/* TODO: create tables */
BEGIN TRANSACTION;

/* user accounts */
CREATE TABLE `accounts` (
	`id`	INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
	`username`	TEXT NOT NULL UNIQUE,
	`password`	TEXT NOT NULL,
	`session`	TEXT UNIQUE
);
INSERT INTO accounts (username, password) VALUES ('kyle', '$2y$10$a20WQXIDhx3NYTm5WOAlAeDZ2OJIPAl8vsxFhCYMmnDHOaYwsmZr.'); /* password: monkey */
INSERT INTO accounts (username, password) VALUES ('miacasey', '$2y$10$YXnMtlZSbC/D66NrM1SBmukN/ozdvgMU0W.iUn/9uC4YtvwhfenX.'); /*password: sf72498*/
INSERT INTO accounts (username, password) VALUES ('dewey_the_turtle', '$2y$10$ouDs9OtIIlY8ViDZ47OZguo4xMZbMjEK4hsigR8o2p/NbN8ZkxeQW'); /*password: wawona94127*/

/* TODO: initial seed data */
/* create documents table */
CREATE TABLE `images` (
	id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
	file_name TEXT NOT NULL,
  description TEXT,
  source TEXT,
	account_id INTEGER
);
INSERT INTO images (id, file_name, description) VALUES ('1', 'bamboo.jpg', 'bamboo forest');
INSERT INTO images (id, file_name, description) VALUES ('2', 'bungee.jpg', 'bungee jump');
INSERT INTO images (id, file_name, description) VALUES ('3', 'crater.jpg', 'crater');
INSERT INTO images (id, file_name, description) VALUES ('4', 'hobbiton.jpg', 'hobbiton');
INSERT INTO images (id, file_name, description) VALUES ('5', 'paris.jpg', 'paris');
INSERT INTO images (id, file_name, description) VALUES ('6', 'sf.jpg', 'sf');
INSERT INTO images (id, file_name, description) VALUES ('7', 'skydive.jpg', 'skydive');
INSERT INTO images (id, file_name, description) VALUES ('8', 'sunrise.jpg', 'sunrise');
INSERT INTO images (id, file_name, description) VALUES ('9', 'sutro.jpg', 'sutro');
INSERT INTO images (id, file_name, description) VALUES ('10', 'tahoe.jpg', 'tahoe');
INSERT INTO images (id, file_name, description) VALUES ('11', 'waterfall.jpg', 'waterfall');
INSERT INTO images (id, file_name, description) VALUES ('12', 'whanga.jpg', 'whanga');

CREATE TABLE `tags` (
  id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
  name TEXT NOT NULL
);

CREATE TABLE `images_tags` (
  id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
  image_id INTEGER,
  tag_id INTEGER
);

COMMIT;
