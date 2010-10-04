DROP TABLE IF EXISTS users, user_data;
DROP TABLE IF EXISTS files, file_data, files_lock;
DROP TABLE IF EXISTS comments, comment_data, comments_lock;
DROP TABLE IF EXISTS pageviews;
DROP TABLE IF EXISTS privatemessages, privatemessage_data;
DROP TABLE IF EXISTS similarities;
#DROP PROCEDURE IF EXISTS SetForgottenPassTimestamp, ValidateForgottenPassTimestamp;
# EAV
CREATE TABLE users (		id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
							name VARCHAR(64) UNIQUE KEY,
							creation_timestamp TIMESTAMP(8) DEFAULT NOW() );
CREATE INDEX user_names ON users (name(8));
CREATE TABLE user_data (	id INT NOT NULL,
							attrib INT NOT NULL,
							options INT NOT NULL,
							intdata INT,
							stringdata TEXT	);
CREATE INDEX userdata_ids ON user_data (id,attrib);
CREATE TABLE files (		id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
							subject VARCHAR(64),
							parent INT NOT NULL, # owner
							type INT NOT NULL, # normal, avatar, etc.
							creation_timestamp TIMESTAMP(8) DEFAULT NOW() );
CREATE INDEX file_ids ON files (id);
CREATE INDEX file_parents ON files (parent);
CREATE TABLE file_data (	id INT NOT NULL,
							attrib INT NOT NULL,
							options INT NOT NULL,
							intdata INT,
							stringdata TEXT	);
CREATE INDEX filedata_attribs ON file_data (id,attrib);
CREATE TABLE files_lock(	id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
							locked BOOL );
#CREATE TABLE comments (		id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
#							subject TEXT,
#							lft INT NOT NULL,
#							rgt INT NOT NULL,
#							creation_timestamp TIMESTAMP(8) DEFAULT NOW() );
CREATE TABLE comments_lock(	id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
							locked BOOL );
CREATE TABLE comments (		id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
							subject TEXT,
							parent INT NOT NULL,
							type INT NOT NULL,
							creation_timestamp TIMESTAMP(8) DEFAULT NOW() );
CREATE INDEX comment_parents ON comments (parent);
CREATE INDEX comment_timestamps ON comments (creation_timestamp);
CREATE TABLE comment_data (	id INT NOT NULL,
							attrib INT NOT NULL,
							options INT NOT NULL,
							intdata INT,
							stringdata TEXT	);
CREATE INDEX comment_attribs ON comment_data (id,attrib);

CREATE TABLE pageviews (	id VARCHAR(32) NOT NULL, # user id or session_id
							comment_id INT NOT NULL, # comment id of resource
							creation_timestamp TIMESTAMP(8) DEFAULT NOW() );
CREATE INDEX pageview_time ON pageviews (creation_timestamp);
CREATE INDEX pageview_id ON pageviews (id (7));
CREATE INDEX pageview_comment_id ON pageviews (comment_id);

#CREATE TABLE privatemessages (	id VARCHAR(32) NOT NULL AUTOINCREMENT,
#								creator_user_id INT NOT NULL,
#								mailbox_user_id INT NOT NULL,
#								subject VARCHAR(64),
#								body TEXT,
#								creation_timestamp TIMESTAMP(8) DEFAULT NOW() );
CREATE TABLE privatemessages (	id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
								subject VARCHAR(64),
								parent INT NOT NULL, # mailbox
								type INT NOT NULL,
# 1: read; 
								flags INT NOT NULL DEFAULT 0,
								creation_timestamp TIMESTAMP(8) DEFAULT NOW() );
CREATE INDEX privatemessages_mailbox_user_id ON privatemessages (parent, creation_timestamp);
CREATE TABLE privatemessage_data (	id INT NOT NULL,
									attrib INT NOT NULL,
									options INT NOT NULL,
									intdata INT,
									stringdata TEXT	);
CREATE INDEX privatemessage_data_id ON privatemessage_data (id, attrib);

CREATE TABLE IF NOT EXISTS migrations (
	id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	creation_timestamp TIMESTAMP(8) DEFAULT NOW() );

CREATE TABLE IF NOT EXISTS similarities (
	cid1 INT NOT NULL, # comment id 1
	cid2 INT NOT NULL, # comment id 2
	val DECIMAL(3,3) NOT NULL # value representing similarity
	);
CREATE INDEX similarity_main ON similarities (cid1, val);

INSERT
	INTO users (name)
	VALUES ('jkoff'), ('tyler');
INSERT
	INTO user_data (id, attrib, intdata, stringdata)
	VALUES	('1','1',NULL,'jkoff'),
			('1','2',NULL,'Jonathan'),
			('1','3',NULL,'Koff'),
			('1','4',NULL,'University of Waterloo'),
			('1','5',NULL,'2013'),
			('1','6',NULL,'jonathankoff@gmail.com'),
			('1','7','1',NULL),
			('1','8',NULL,'29d1d9c975cc38353ff25336f95d83559aa7a909ec8aefe2e96ed03b312d7b4d');
INSERT
	INTO user_data (id, attrib, intdata, stringdata)
	VALUES	('2','1',NULL,'tyler'),
			('2','2',NULL,'Tyler'),
			('2','3',NULL,'Freedman'),
			('2','4',NULL,'Ryerson University'),
			('2','5',NULL,'2014?'),
			('2','6',NULL,'t.freedman@gmail.com'),
			('2','7','1',NULL),
			('2','8',NULL,'fcdbb6fa38d66468fff683cb992bc875280ae44963af2cfdc93a89c9d6d50c9a');
			#mudkipz

#INSERT
#	INTO comments (id, subject, lft, rgt)
#	VALUES	('1', 'root', '1', '2'); # root
INSERT
	INTO comments_lock (id, locked)
	VALUES	('1', 'false'); # root
INSERT
	INTO comments (id, subject, parent)
	VALUES	('1', 'root', '1'); # root
INSERT
	INTO migrations ()
	VALUES ();
# Favourite Courses
INSERT
	INTO user_data (id, attrib, intdata)
	VALUES	(1, 12, 3),
			(1, 12, 118),
			(1, 12, 1987),
			(1, 12, 1988);

