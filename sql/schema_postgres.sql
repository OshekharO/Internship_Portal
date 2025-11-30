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
  phone VARCHAR(20),
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
('Digital Marketing Internship', 'Learn SEO, SEM and social media strategies.', '1 Month'),
('Mobile App Development Internship', 'Build Android and iOS apps using React Native and Flutter.', '4 Months'),
('UI/UX Design Internship', 'Create user-centered designs using Figma and Adobe XD.', '2 Months'),
('Machine Learning Internship', 'Develop ML models using TensorFlow and scikit-learn.', '6 Months'),
('Cloud Computing Internship', 'Work with AWS, Azure, and GCP cloud services.', '3 Months'),
('DevOps Engineering Internship', 'Learn CI/CD pipelines, Docker, and Kubernetes.', '4 Months'),
('Cybersecurity Internship', 'Explore network security, ethical hacking, and vulnerability assessment.', '3 Months'),
('Content Writing Internship', 'Create engaging blog posts, articles, and social media content.', '1 Month'),
('Graphic Design Internship', 'Design logos, banners, and marketing materials using Adobe Creative Suite.', '2 Months'),
('Backend Development Internship', 'Build RESTful APIs using Node.js, Python, or Java.', '3 Months'),
('Frontend Development Internship', 'Create responsive web interfaces using React and Vue.js.', '2 Months'),
('Full Stack Development Internship', 'Work on both frontend and backend with MERN/MEAN stack.', '5 Months'),
('Database Administration Internship', 'Manage and optimize MySQL, PostgreSQL, and MongoDB databases.', '3 Months'),
('AI Research Internship', 'Conduct research in natural language processing and computer vision.', '6 Months'),
('Blockchain Development Internship', 'Build decentralized applications and smart contracts.', '4 Months'),
('Game Development Internship', 'Create games using Unity and Unreal Engine.', '3 Months'),
('Quality Assurance Internship', 'Perform manual and automated testing using Selenium and Jest.', '2 Months'),
('Product Management Internship', 'Learn product lifecycle management and agile methodologies.', '3 Months'),
('Business Analytics Internship', 'Analyze business data using SQL, Tableau, and Power BI.', '2 Months'),
('Video Editing Internship', 'Edit and produce video content using Premiere Pro and After Effects.', '1 Month'),
('Social Media Management Internship', 'Manage social media accounts and create engagement strategies.', '2 Months'),
('SEO Specialist Internship', 'Optimize website content for search engines and improve rankings.', '1 Month'),
('HR Operations Internship', 'Assist in recruitment, onboarding, and employee engagement activities.', '3 Months');

-- Default admin (username: admin, password: admin)
INSERT INTO admins (username, password) VALUES ('admin', 'admin');
