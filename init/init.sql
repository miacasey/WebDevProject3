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
INSERT INTO images (id, file_name, description, account_id) VALUES ('1', 'bamboo.jpg', 'Bamboo Forest, Maui', '2');
INSERT INTO images (id, file_name, description, account_id) VALUES ('2', 'bungee.jpg', 'Bungee Jumping in Taupo, NZ', '2');
INSERT INTO images (id, file_name, description, account_id) VALUES ('3', 'crater.jpg', 'Mount Haleakala, Maui', '3');
INSERT INTO images (id, file_name, description, account_id) VALUES ('4', 'hobbiton.jpg', 'Hobbiton Movie Set, NZ', '3');
INSERT INTO images (id, file_name, description) VALUES ('5', 'paris.jpg', 'Eiffel Tower, Paris');
INSERT INTO images (id, file_name, description) VALUES ('6', 'sf.jpg', 'San Francisco Skyline');
INSERT INTO images (id, file_name, description) VALUES ('7', 'skydive.jpg', 'Skydiving in Santa Cruz, CA');
INSERT INTO images (id, file_name, description) VALUES ('8', 'sunrise.jpg', 'Sunrise over Mount Haleakala');
INSERT INTO images (id, file_name, description) VALUES ('9', 'sutro.jpg', 'Sutro Baths, San Francisco');
INSERT INTO images (id, file_name, description) VALUES ('10', 'tahoe.jpg', 'Lake Tahoe, CA');
INSERT INTO images (id, file_name, description) VALUES ('11', 'waterfall.jpg', 'Waterfall in Hana, Maui');
INSERT INTO images (id, file_name, description) VALUES ('12', 'whanga.jpg', 'Whangamata, NZ');
INSERT INTO images (id, file_name, description) VALUES ('13', 'bigbeach.jpg', 'Big Beach, Maui');
INSERT INTO images (id, file_name, description) VALUES ('14', 'ireland.jpg', 'Valentia Island, Ireland');
INSERT INTO images (id, file_name, description) VALUES ('15', 'oceanbeach.jpg', 'Ocean Beach, San Francisco');
INSERT INTO images (id, file_name, description) VALUES ('16', 'pacifica.jpg', 'Pacifica Cliffs, CA');

CREATE TABLE `tags` (
  id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
  name TEXT NOT NULL
);
INSERT INTO tags (id, name) VALUES ('1', 'Ocean');
INSERT INTO tags (id, name) VALUES ('2', 'Nature');
INSERT INTO tags (id, name) VALUES ('3', 'New Zealand');
INSERT INTO tags (id, name) VALUES ('4', 'San Francisco');
INSERT INTO tags (id, name) VALUES ('5', 'Hawaii');

CREATE TABLE `images_tags` (
  id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
  image_id INTEGER,
  tag_id INTEGER
);
INSERT INTO images_tags (id, image_id, tag_id) VALUES ('1', '9', '1');
INSERT INTO images_tags (id, image_id, tag_id) VALUES ('2', '12', '1');
INSERT INTO images_tags (id, image_id, tag_id) VALUES ('3', '13', '1');
INSERT INTO images_tags (id, image_id, tag_id) VALUES ('4', '15', '1');
INSERT INTO images_tags (id, image_id, tag_id) VALUES ('5', '16', '1');
INSERT INTO images_tags (id, image_id, tag_id) VALUES ('6', '1', '2');
INSERT INTO images_tags (id, image_id, tag_id) VALUES ('7', '3', '2');
INSERT INTO images_tags (id, image_id, tag_id) VALUES ('8', '14', '2');
INSERT INTO images_tags (id, image_id, tag_id) VALUES ('9', '12', '3');
INSERT INTO images_tags (id, image_id, tag_id) VALUES ('10', '4', '3');
INSERT INTO images_tags (id, image_id, tag_id) VALUES ('11', '2', '3');
INSERT INTO images_tags (id, image_id, tag_id) VALUES ('12', '15', '4');
INSERT INTO images_tags (id, image_id, tag_id) VALUES ('13', '6', '4');
INSERT INTO images_tags (id, image_id, tag_id) VALUES ('14', '9', '4');
INSERT INTO images_tags (id, image_id, tag_id) VALUES ('15', '1', '5');
INSERT INTO images_tags (id, image_id, tag_id) VALUES ('16', '3', '5');
INSERT INTO images_tags (id, image_id, tag_id) VALUES ('17', '8', '5');
INSERT INTO images_tags (id, image_id, tag_id) VALUES ('18', '11', '5');
INSERT INTO images_tags (id, image_id, tag_id) VALUES ('19', '13', '5');

COMMIT;
