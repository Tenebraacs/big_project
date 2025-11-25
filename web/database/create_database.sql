CREATE DATABASE JarProject
CHARACTER SET utf8
COLLATE utf8_hungarian_ci;

USE JarProject;

CREATE TABLE user (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(32),
    password VARCHAR(32),
    email VARCHAR(64),
    userxp INT
);

CREATE TABLE jars (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(32),
    shortdesc VARCHAR(128),
    author INT,
    description TEXT,
    picture VARCHAR(256),
    totalviews INT,
    compcount INT,
    creationdate DATETIME,
    FOREIGN KEY (author) REFERENCES user(id)
);

CREATE TABLE completions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    jarid INT,
    userid INT,
    picture VARCHAR(256),
    description VARCHAR(128),
    likes INT,
    date DATETIME,
    FOREIGN KEY (jarid) REFERENCES jars(id),
    FOREIGN KEY (userid) REFERENCES user(id)
);

CREATE TABLE myjars (
    id INT PRIMARY KEY AUTO_INCREMENT,
    userid INT,
    jarid INT,
    FOREIGN KEY (userid) REFERENCES user(id),
    FOREIGN KEY (jarid) REFERENCES jars(id)
);

CREATE TABLE jar_groups (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(32),
    shortdesc VARCHAR(128),
    description VARCHAR(254),
    jarid_array TEXT,
    allowedid_array TEXT
);