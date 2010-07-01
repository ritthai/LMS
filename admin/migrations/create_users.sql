DROP TABLE IF EXISTS users, user_data, user_attribs;
# EAV
CREATE TABLE users (	id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
						name VARCHAR(64),
						creation_timestamp TIMESTAMP(8) DEFAULT NOW() );
CREATE TABLE user_data (	id INT NOT NULL,
							attrib INT NOT NULL,
							intdata INT,
							stringdata VARCHAR(255)	);
#CREATE TABLE user_attribs (	id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
							#name VARCHAR(64) UNIQUE KEY,
							#type VARCHAR(64) );
#INSERT
	#INTO user_attribs (name, type)
	#VALUES	( 'name', 'string' ),
			#( 'email', 'string' ),
			#( 'role', 'string' ),
			#( 'password', 'string' );
	
