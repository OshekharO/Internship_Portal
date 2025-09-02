-- Drop if exists
DROP TABLE IF EXISTS certificates, applications, internships, admins CASCADE;

CREATE TABLE internships (
  id SERIAL PRIMARY KEY,
  title VARCHAR(100),
  description TEXT,
  duration VARCHAR(50)
);

CREATE TABLE applications (
  id SERIAL PRIMARY KEY,
  internship_id INT REFERENCES internships(id),
  name VARCHAR(100),
  email VARCHAR(100),
  resume VARCHAR(255) NULL,
  status VARCHAR(10) DEFAULT 'pending' CHECK (status IN ('pending','selected','rejected'))
);

CREATE TABLE certificates (
  id SERIAL PRIMARY KEY,
  application_id INT REFERENCES applications(id),
  certificate_code VARCHAR(50) UNIQUE,
  issue_date DATE
);

CREATE TABLE admins (
  id SERIAL PRIMARY KEY,
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
