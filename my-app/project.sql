-- Reset tables
DROP TABLE IF EXISTS order_items;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS cart;
DROP TABLE IF EXISTS books;
DROP TABLE IF EXISTS customers;
DROP TABLE IF EXISTS memberships;

-- Memberships
CREATE TABLE memberships (
    membership_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) UNIQUE NOT NULL,
    benefits TEXT
);

INSERT INTO memberships (name, benefits)
VALUES 
('Basic', 'Access to all books'),
('Premium', 'Discounts, early access, and free shipping');


-- Customers
CREATE TABLE customers (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    firstname VARCHAR(100) NOT NULL,
    lastname VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    membership_id INT,
    FOREIGN KEY (membership_id) REFERENCES memberships(membership_id)
);

INSERT INTO customers (firstname, lastname, email, password, membership_id)
VALUES 
('Jason', 'Bartholomew', 'jason@email.com', 'aBcD123', 2),
('Daniel', 'Erickson', 'daniel@email.com', 'SChab00dl3', 1);


-- Books (formerly products)
CREATE TABLE books (
    book_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(255),
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    stock INT NOT NULL,
    image_url VARCHAR(255),
    genre VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Books
INSERT INTO books (title, author, description, price, stock, image_url, genre)
VALUES
-- Fiction
('The Great Gatsby', 'F. Scott Fitzgerald', 'Classic novel set in the Jazz Age.', 10.99, 50, 'gatsby.jpg', 'Fiction'),
('To Kill a Mockingbird', 'Harper Lee', 'A story about racial injustice.', 12.99, 40, 'mockingbird.jpg', 'Fiction'),

-- Fantasy
('Harry Potter and the Sorcerer''s Stone', 'J.K. Rowling', 'A young wizard begins his journey.', 14.99, 60, 'hp1.jpg', 'Fantasy'),
('The Hobbit', 'J.R.R. Tolkien', 'A hobbit goes on an adventure.', 13.49, 35, 'hobbit.jpg', 'Fantasy'),

-- Sci-Fi
('Dune', 'Frank Herbert', 'Epic science fiction saga.', 15.99, 30, 'dune.jpg', 'Sci-Fi'),
('Ender''s Game', 'Orson Scott Card', 'A child prodigy trains for war.', 11.99, 45, 'ender.jpg', 'Sci-Fi'),

-- Mystery
('Gone Girl', 'Gillian Flynn', 'A psychological thriller.', 10.49, 25, 'gonegirl.jpg', 'Mystery'),
('Sherlock Holmes', 'Arthur Conan Doyle', 'Detective mystery stories.', 9.99, 50, 'sherlock.jpg', 'Mystery'),

-- Non-Fiction
('Sapiens', 'Yuval Noah Harari', 'A brief history of humankind.', 16.99, 20, 'sapiens.jpg', 'Non-Fiction'),
('Atomic Habits', 'James Clear', 'Build good habits, break bad ones.', 18.99, 30, 'atomic.jpg', 'Non-Fiction');


-- Cart
CREATE TABLE cart (
    cart_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    book_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,

    FOREIGN KEY (user_id) REFERENCES customers(user_id),
    FOREIGN KEY (book_id) REFERENCES books(book_id)
);

-- Orders
CREATE TABLE orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_price DECIMAL(10,2),
    status ENUM('Pending', 'Processing', 'Shipped', 'Cancelled') DEFAULT 'Pending',
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES customers(user_id)
);

-- Order Items
CREATE TABLE order_items (
    order_item_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    book_id INT NOT NULL,
    quantity INT NOT NULL,

    FOREIGN KEY (order_id) REFERENCES orders(order_id),
    FOREIGN KEY (book_id) REFERENCES books(book_id)
);