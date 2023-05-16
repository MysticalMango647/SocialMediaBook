CREATE DATABASE IF NOT EXISTS yunas_magsi_syscbook;
USE yunas_magsi_syscbook;

-- 1. users_info
CREATE TABLE USERS_INFO(
	student_ID INT(10) NOT NULL AUTO_INCREMENT,
    student_email VARCHAR(150) NOT NULL,
    first_name VARCHAR(150) NOT NULL,
    last_name VARCHAR(150) NOT NULL,
    DOB DATE NOT NULL,
    PRIMARY KEY (student_ID)
);
ALTER TABLE USERS_INFO AUTO_INCREMENT=100100;

-- INSERT INTO USERS_INFO VALUES ('10','JOHN@mail.com', 'john','Cake','1998-05-01');
-- INSERT INTO USERS_INFO VALUES ('26','HOHN@mail.com', 'Hohn','Cake','1999-05-01');

-- 2. users_program
CREATE TABLE users_program(
	student_ID INT(10) NOT NULL,
    Program VARCHAR(50) NOT NULL,
    FOREIGN KEY(student_ID) REFERENCES users_info(student_ID)
);
ALTER TABLE users_program ADD PRIMARY KEY(student_ID);

-- 3. users_avatar
create TABLE users_avatar(
	student_ID INT(10) NOT NULL,
    avatar INT(1) NOT NULL,
    FOREIGN KEY(student_ID) REFERENCES users_info(student_ID)
);
ALTER TABLE users_avatar ADD PRIMARY KEY(student_ID);

-- 4. users_address
create table users_address(
	student_ID INT(10) NOT NULL,
    street_number INT(5) NOT NULL,
    street_name VARCHAR(150) NOT NULL,
    city VARCHAR(30) NOT NULL,
    provence VARCHAR(2) NOT NULL,
    postal_code VARCHAR(7) NOT NULL,
    FOREIGN KEY(student_ID) REFERENCES users_info(student_ID)
);
ALTER TABLE users_address ADD PRIMARY KEY users_post(student_ID);

-- 5. users_posts
create table users_posts(
	post_ID INT(100) NOT NULL AUTO_INCREMENT,
    student_ID INT(10) NOT NULL,
    new_post TEXT(1000) NOT NULL,
    post_date TIMESTAMP,
    PRIMARY KEY(post_ID), 
    FOREIGN KEY(student_ID) REFERENCES users_info(student_ID)
);


-- 6. users_passwords
create table users_passwords(
    student_ID INT(10) NOT NULL,
    password VARCHAR(255) NOT NULL,
    FOREIGN KEY(student_ID) REFERENCES users_info(student_ID)
);
ALTER TABLE users_passwords ADD PRIMARY KEY users_info(student_ID);

-- 7. users_permissions

CREATE TABLE users_permissions(
    student_ID INT(10) NOT NULL,
    account_type INT(1) NOT NULL,
    FOREIGN KEY(student_ID) REFERENCES users_info(student_ID)
);
ALTER TABLE users_permissions  ALTER account_type  SET DEFAULT 1;

-- Extra for Admin privlages
/*
Update users_permissions
set account_type = 0
WHERE student_ID =100100;
*/