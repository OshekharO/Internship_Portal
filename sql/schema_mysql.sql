-- Drop if exists
DROP TABLE IF EXISTS certificates, applications, internships, admins;

CREATE TABLE internships (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(100),
  description TEXT,
  duration VARCHAR(50)
);

CREATE TABLE applications (
  id INT AUTO_INCREMENT PRIMARY KEY,
  internship_id INT,
  name VARCHAR(100),
  email VARCHAR(100),
  phone VARCHAR(20),
  status ENUM('pending','selected','rejected') DEFAULT 'pending',
  FOREIGN KEY (internship_id) REFERENCES internships(id)
);

CREATE TABLE certificates (
  id INT AUTO_INCREMENT PRIMARY KEY,
  application_id INT,
  certificate_code VARCHAR(50) UNIQUE,
  issue_date DATE,
  FOREIGN KEY (application_id) REFERENCES applications(id)
);

CREATE TABLE admins (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) UNIQUE,
  password VARCHAR(255)
);

-- Seed data
INSERT INTO internships (title, description, duration) VALUES
('Web Development Internship', 'Work with HTML, CSS, JS to build projects.', '3 Months'),
('Data Science Internship', 'Analyze datasets using Python and ML.', '2 Months'),
('Digital Marketing Internship', 'Learn SEO, SEM and social media strategies.', '1 Month');

-- Default admin (username: admin, password: admin)
INSERT INTO admins (username, password) VALUES ('admin', 'admin');
