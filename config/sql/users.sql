-- insert sql to users table it have
-- id, name,username,phone, email,password,is_admin(true)created_at
DELIMITER $$

CREATE PROCEDURE InsertUsers()
BEGIN
    DECLARE i INT DEFAULT 1;

    WHILE i <= 100 DO
        INSERT INTO `users` (`name`, `username`, `phone`, `email`, `password`, `is_admin`, `created_at`)
        VALUES (CONCAT('User ', i), CONCAT('user', i), '07501842910', CONCAT('user', i, '@gmail.com'), 'muhammad', 1, NOW());
        SET i = i + 1;
    END WHILE;
END $$

DELIMITER ;

CALL InsertUsers();