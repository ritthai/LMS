DROP TABLE IF EXISTS users, user_data;
# EAV
CREATE TABLE users (	id INT NOT NULL AUTO_INCREMENT,
						name VARCHAR(64),
						creation_timestamp TIMESTAMP(8) DEFAULT NOW(),
						primary key (id) );
CREATE TABLE user_data (	userid INT NOT NULL,
							attrib INT NOT NULL,
							intdata INT,
							stringdata VARCHAR(255)	);
