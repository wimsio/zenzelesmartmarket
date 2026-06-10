-- ===== ZENZELE SMART MARKET — FULL DATABASE SCHEMA =====
-- Run: mysql -u root -p < schema.sql
-- Or import via phpMyAdmin / cPanel

CREATE DATABASE IF NOT EXISTS zenzele_db
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
USE zenzele_db;

CREATE TABLE IF NOT EXISTS users (
  id               INT           AUTO_INCREMENT PRIMARY KEY,
  name             VARCHAR(120)  NOT NULL,
  email            VARCHAR(180)  NOT NULL UNIQUE,
  password         VARCHAR(255)  NOT NULL,
  country          VARCHAR(10),
  city             VARCHAR(100),
  category         VARCHAR(100),
  bio              TEXT,
  skills           JSON,
  wallet           VARCHAR(200),
  training_wanted  VARCHAR(200),
  open_for_work    TINYINT(1)    DEFAULT 0,
  has_mentor       TINYINT(1)    DEFAULT 0,
  avatar           VARCHAR(20)   DEFAULT '🧑🏾‍💼',
  audio_url        VARCHAR(500),
  followers        INT           DEFAULT 0,
  likes            INT           DEFAULT 0,
  views            INT           DEFAULT 0,
  donations        INT           DEFAULT 0,
  created_at       TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
  updated_at       TIMESTAMP     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS nfts (
  id           INT           AUTO_INCREMENT PRIMARY KEY,
  user_id      INT           NOT NULL,
  title        VARCHAR(200)  NOT NULL,
  description  TEXT,
  icon         VARCHAR(20)   DEFAULT '🏆',
  category     VARCHAR(100),
  support_goal VARCHAR(200),
  image_url    VARCHAR(500),
  policy_id    VARCHAR(200),
  asset_name   VARCHAR(200),
  tx_hash      VARCHAR(300),
  metadata     JSON,
  network      VARCHAR(20)   DEFAULT 'testnet',
  minted_at    TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS donations (
  id           INT            AUTO_INCREMENT PRIMARY KEY,
  to_user_id   INT            NOT NULL,
  from_name    VARCHAR(120)   DEFAULT 'Anonymous',
  amount_ada   DECIMAL(18,6)  NOT NULL,
  message      TEXT,
  tx_ref       VARCHAR(200),
  network      VARCHAR(20)    DEFAULT 'testnet',
  donated_at   TIMESTAMP      DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (to_user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS follows (
  follower_id  INT       NOT NULL,
  following_id INT       NOT NULL,
  created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (follower_id, following_id),
  FOREIGN KEY (follower_id)  REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (following_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS likes (
  user_id      INT       NOT NULL,
  profile_id   INT       NOT NULL,
  created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (user_id, profile_id),
  FOREIGN KEY (user_id)    REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (profile_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS training_requests (
  id         INT          AUTO_INCREMENT PRIMARY KEY,
  user_id    INT          NOT NULL,
  area       VARCHAR(200) NOT NULL,
  details    TEXT,
  status     ENUM('pending','matched','completed') DEFAULT 'pending',
  created_at TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Indexes
CREATE INDEX idx_users_category  ON users(category);
CREATE INDEX idx_users_country   ON users(country);
CREATE INDEX idx_users_open_work ON users(open_for_work);
CREATE INDEX idx_users_mentor    ON users(has_mentor);
CREATE INDEX idx_nfts_user       ON nfts(user_id);
CREATE INDEX idx_donations_to    ON donations(to_user_id);
CREATE INDEX idx_training_status ON training_requests(status);
