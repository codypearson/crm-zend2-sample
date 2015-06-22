USE crm;

INSERT IGNORE INTO user
(username, password)
VALUES
('testuser', SHA2('t3stp455', 256));