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
							name VARCHAR(64),
							creation_timestamp TIMESTAMP(8) DEFAULT NOW() );
CREATE TABLE file_data (	id INT NOT NULL,
							attrib INT NOT NULL,
							options INT NOT NULL,
							intdata INT,
							stringdata TEXT	);
CREATE TABLE comments (		id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
							subject TEXT,
							lft INT NOT NULL,
							rgt INT NOT NULL,
							creation_timestamp TIMESTAMP(8) DEFAULT NOW() );
CREATE TABLE comment_data (	id INT NOT NULL,
							attrib INT NOT NULL,
							options INT NOT NULL,
							intdata INT,
							stringdata TEXT	);
INSERT
	INTO users (name)
	VALUES ('jkoff');
INSERT
	INTO user_data (id, attrib, intdata, stringdata)
	VALUES	('1','1',NULL,'jkoff'),
			('1','2',NULL,'jonathankoff@gmail.com'),
			('1','3','1',NULL),
			('1','4',NULL,'5f5bf79f826c28089749b8f49d82360b27e9710918b4a6b4a763f95c4d20bf4d');

INSERT
	INTO comments (id, subject, lft, rgt)
	VALUES	('1', 'root', '1', '2') # root

