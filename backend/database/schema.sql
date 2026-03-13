CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100),
  email VARCHAR(100) UNIQUE,
  password VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE conversations (
  id INT AUTO_INCREMENT PRIMARY KEY,
  type ENUM('private','group') DEFAULT 'private',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE conversation_participants (
  id INT AUTO_INCREMENT PRIMARY KEY,
  conversation_id INT,
  user_id INT,
  FOREIGN KEY (conversation_id) REFERENCES conversations(id),
  FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE messages (
  id INT AUTO_INCREMENT PRIMARY KEY,
  conversation_id INT,
  user_id INT,
  message TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (conversation_id) REFERENCES conversations(id),
  FOREIGN KEY (user_id) REFERENCES users(id)
);

-- seed users
INSERT INTO users (name,email,password) VALUES
('Jefferson','jefferson@email.com','$2y$10$KwkHoOD2qHY.AZ.ir6k1yunmoNZNzt1gkNXZdpsbKwBGGollnW8pG'),
('Alice','alice@email.com','$2y$10$KwkHoOD2qHY.AZ.ir6k1yunmoNZNzt1gkNXZdpsbKwBGGollnW8pG'),
('Bob','bob@email.com','$2y$10$KwkHoOD2qHY.AZ.ir6k1yunmoNZNzt1gkNXZdpsbKwBGGollnW8pG');

-- seed conversation
INSERT INTO conversations (type) VALUES ('private');

-- seed participants
INSERT INTO conversation_participants (conversation_id,user_id) VALUES
(1,1),
(1,2);

-- seed messages
INSERT INTO messages (conversation_id,user_id,message) VALUES
(1,1,'Olá Alice'),
(1,2,'Olá Jefferson'),
(1,1,'Bem-vinda ao chat realtime');