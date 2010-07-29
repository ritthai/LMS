DROP TABLE IF EXISTS users, user_data;
DROP TABLE IF EXISTS files, file_data;
DROP TABLE IF EXISTS comments, comment_data;
#DROP PROCEDURE IF EXISTS SetForgottenPassTimestamp, ValidateForgottenPassTimestamp;
# EAV
CREATE TABLE users (		id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
							name VARCHAR(64) UNIQUE KEY,
							creation_timestamp TIMESTAMP(8) DEFAULT NOW() );
CREATE TABLE user_data (	id INT NOT NULL,
							attrib INT NOT NULL,
							options INT NOT NULL,
							intdata INT,
							stringdata TEXT	);
CREATE TABLE files (		id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
							subject VARCHAR(64),
							parent INT NOT NULL, # owner
							type INT NOT NULL, # normal, avatar, etc.
							creation_timestamp TIMESTAMP(8) DEFAULT NOW() );
CREATE TABLE file_data (	id INT NOT NULL,
							attrib INT NOT NULL,
							options INT NOT NULL,
							intdata INT,
							stringdata TEXT	);
#CREATE TABLE comments (		id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
#							subject TEXT,
#							lft INT NOT NULL,
#							rgt INT NOT NULL,
#							creation_timestamp TIMESTAMP(8) DEFAULT NOW() );
CREATE TABLE comments (		id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
							subject TEXT,
							parent INT NOT NULL,
							type INT NOT NULL,
							creation_timestamp TIMESTAMP(8) DEFAULT NOW() );
CREATE TABLE comment_data (	id INT NOT NULL,
							attrib INT NOT NULL,
							options INT NOT NULL,
							intdata INT,
							stringdata TEXT	);
CREATE TABLE IF NOT EXISTS migrations (
	id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	creation_timestamp TIMESTAMP(8) DEFAULT NOW() );

INSERT
	INTO users (name)
	VALUES ('jkoff');
INSERT
	INTO user_data (id, attrib, intdata, stringdata)
	VALUES	('1','1',NULL,'jkoff'),
			('1','2',NULL,'Jonathan Koff'),
			('1','3',NULL,'University of Waterloo'),
			('1','4',NULL,'2013'),
			('1','5',NULL,'jonathankoff@gmail.com'),
			('1','6','1',NULL),
			('1','7',NULL,'5f5bf79f826c28089749b8f49d82360b27e9710918b4a6b4a763f95c4d20bf4d');
INSERT
	INTO user_data (id, attrib, intdata, stringdata)
	VALUES	('2','1',NULL,'tyler'),
			('2','2',NULL,'Tyler Freedman'),
			('2','3',NULL,'Ryerson University'),
			('2','4',NULL,'2014?'),
			('2','5',NULL,'t.freedman@gmail.com'),
			('2','6','1',NULL),
			('2','7',NULL,'7cc579d40474f703aa6c86c5790f3d79c14ed6be48b639840f64fe7b1335313c');

#INSERT
#	INTO comments (id, subject, lft, rgt)
#	VALUES	('1', 'root', '1', '2'); # root
INSERT
	INTO comments (id, subject, parent)
	VALUES	('1', 'root', '1'); # root
INSERT
	INTO migrations ()
	VALUES ();

