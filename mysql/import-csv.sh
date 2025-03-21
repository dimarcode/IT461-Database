#!/bin/bash
set -e

# Create table if it doesn't exist
mysql -u root -p"$MYSQL_ROOT_PASSWORD" $MYSQL_DATABASE <<-'EOSQL'
  
    DROP TABLE IF EXISTS `customers`;

    CREATE TABLE IF NOT EXISTS customers (
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
        item_id INT,
        quantity INT,
        price DECIMAL(10,2) NOT NULL
    );

    DROP TABLE IF EXISTS `order_list`;

    CREATE TABLE IF NOT EXISTS order_list (
        order_id INT AUTO_INCREMENT PRIMARY KEY,
        customer_id INT NOT NULL,
        total_price DECIMAL(10,2) NOT NULL,
        order_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        pickup_date TIMESTAMP NOT NULL
    );

    DROP TABLE IF EXISTS `items`;

    CREATE TABLE IF NOT EXISTS items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        item_name VARCHAR(200) NOT NULL,
        price VARCHAR(200) NOT NULL
    );

    -- Only add indexes, not primary keys again
    ALTER TABLE `order_details`
        ADD KEY `order_id` (`order_id`),
        ADD KEY `item_id` (`item_id`);

    ALTER TABLE `order_list`
        ADD KEY `customer_id` (`customer_id`);

    -- Set auto_increment values
    ALTER TABLE `customers` AUTO_INCREMENT=222;
    ALTER TABLE `order_details` AUTO_INCREMENT=6;
    ALTER TABLE `order_list` AUTO_INCREMENT=4;
    ALTER TABLE `items` AUTO_INCREMENT=33;

    -- Add foreign key constraints
    ALTER TABLE `order_details`
        ADD CONSTRAINT `order_details_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `order_list` (`order_id`),
        ADD CONSTRAINT `order_details_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`);

    ALTER TABLE `order_list`
        ADD CONSTRAINT `order_list_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`);

EOSQL

# Import the CSV data
mysql -u root -p"$MYSQL_ROOT_PASSWORD" $MYSQL_DATABASE -e "
  LOAD DATA INFILE '/var/lib/mysql-files/customers.csv' 
  INTO TABLE customers 
  FIELDS TERMINATED BY ',' 
  ENCLOSED BY '\"' 
  LINES TERMINATED BY '\n' 
  IGNORE 1 ROWS;"

mysql -u root -p"$MYSQL_ROOT_PASSWORD" $MYSQL_DATABASE -e "
  LOAD DATA INFILE '/var/lib/mysql-files/items.csv' 
  INTO TABLE items 
  FIELDS TERMINATED BY ',' 
  ENCLOSED BY '\"'
  LINES TERMINATED BY '\n' 
  IGNORE 1 ROWS;"