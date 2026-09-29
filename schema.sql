CREATE DATABASE IF NOT EXISTS spillthetea CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE spillthetea;

CREATE TABLE IF NOT EXISTS users (
  username      VARCHAR(50) PRIMARY KEY,
  password_hash VARCHAR(255) NOT NULL,
  display_name  VARCHAR(100) NOT NULL,
  email         VARCHAR(150),
  bio           TEXT,
  joined        BIGINT NOT NULL
);

CREATE TABLE IF NOT EXISTS memes (
  id            CHAR(36) PRIMARY KEY,
  uploader      VARCHAR(50) NOT NULL,
  uploader_name VARCHAR(100) NOT NULL,
  image_path    VARCHAR(255) NOT NULL,
  caption       TEXT,
  category      ENUM('Relatable','Trending','Random','Dark Humor') NOT NULL,
  ts            BIGINT NOT NULL,
  FOREIGN KEY (uploader) REFERENCES users(username)
);

CREATE TABLE IF NOT EXISTS reactions (
  meme_id  CHAR(36)    NOT NULL,
  username VARCHAR(50) NOT NULL,
  value    TINYINT     NOT NULL,          -- 1 = like, -1 = dislike
  PRIMARY KEY (meme_id, username),        -- one reaction per user per meme
  FOREIGN KEY (meme_id)  REFERENCES memes(id)      ON DELETE CASCADE,
  FOREIGN KEY (username) REFERENCES users(username) ON DELETE CASCADE
);
