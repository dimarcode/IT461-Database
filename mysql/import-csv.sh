#!/bin/bash
set -e

# Create table if it doesn't exist
mysql -u root -p"$MYSQL_ROOT_PASSWORD" $MYSQL_DATABASE <<-'EOSQL'
  
    DROP TABLE IF EXISTS `data_info_user`;

    CREATE TABLE IF NOT EXISTS data_info_user (
        id INT AUTO_INCREMENT PRIMARY KEY,
        first_name VARCHAR(200),
        last_name VARCHAR(200),
        address VARCHAR(200),
        city VARCHAR(200),
        state VARCHAR(200),
        zip VARCHAR(20),
        phone1 VARCHAR(20),
        email VARCHAR(200)
    );

    DROP TABLE IF EXISTS `order_details`;

    CREATE TABLE IF NOT EXISTS order_details (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT NOT NULL,
        product_id INT,
        quantity INT,
        price DECIMAL(10,2) NOT NULL
    );

    DROP TABLE IF EXISTS `order_list`;

    CREATE TABLE IF NOT EXISTS order_list (
        order_id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        total_price DECIMAL(10,2) NOT NULL,
        order_date timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
    );

     DROP TABLE IF EXISTS `price_list_product`;

    CREATE TABLE IF NOT EXISTS price_list_product (
        id INT AUTO_INCREMENT PRIMARY KEY,
        item_name VARCHAR(200) NOT NULL,
        price VARCHAR(200) NOT NULL
    );

    -- Only add indexes, not primary keys again
    ALTER TABLE `order_details`
        ADD KEY `order_id` (`order_id`),
        ADD KEY `product_id` (`product_id`);

    ALTER TABLE `order_list`
        ADD KEY `user_id` (`user_id`);

    -- Set auto_increment values
    ALTER TABLE `data_info_user` AUTO_INCREMENT=222;
    ALTER TABLE `order_details` AUTO_INCREMENT=6;
    ALTER TABLE `order_list` AUTO_INCREMENT=4;
    ALTER TABLE `price_list_product` AUTO_INCREMENT=33;

    -- Add foreign key constraints
    ALTER TABLE `order_details`
        ADD CONSTRAINT `order_details_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `order_list` (`order_id`),
        ADD CONSTRAINT `order_details_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `price_list_product` (`id`);

    ALTER TABLE `order_list`
        ADD CONSTRAINT `order_list_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `data_info_user` (`id`);

EOSQL

# Import the CSV data
mysql -u root -p"$MYSQL_ROOT_PASSWORD" $MYSQL_DATABASE -e "
  LOAD DATA INFILE '/var/lib/mysql-files/customers.csv' 
  INTO TABLE data_info_user 
  FIELDS TERMINATED BY ',' 
  ENCLOSED BY '\"' 
  LINES TERMINATED BY '\n' 
  IGNORE 1 ROWS;"

mysql -u root -p"$MYSQL_ROOT_PASSWORD" $MYSQL_DATABASE -e "
  LOAD DATA INFILE '/var/lib/mysql-files/items.csv' 
  INTO TABLE price_list_product 
  FIELDS TERMINATED BY ',' 
  ENCLOSED BY '\"'
  LINES TERMINATED BY '\n' 
  IGNORE 1 ROWS;"