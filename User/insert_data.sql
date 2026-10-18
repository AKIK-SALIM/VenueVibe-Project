-- ============================================================
-- VenueVibe - Data Insertion Script (Seed Data)
-- File: insert_data.sql
-- ============================================================

USE venuevibe;

-- ------------------------------------------------------------
-- 1. SEED USERS (password for all: password123)
-- ------------------------------------------------------------
INSERT INTO users (full_name, email, password_hash, role, is_admin, student_id, department, phone) VALUES
('Campus Admin', 'admin@uiu.ac.bd', '$2y$10$3TZsHsL03vh.7k8O8PdaE.MpZr2ma4XHlXseFipWYamjQWNkGJPx6', 'admin', 1, NULL, 'Campus Facilities Administration', '+880 1700-000000'),
('Fahim Ahmed', 'student@uiu.ac.bd', '$2y$10$3TZsHsL03vh.7k8O8PdaE.MpZr2ma4XHlXseFipWYamjQWNkGJPx6', 'student', 0, '011 221 045', 'Computer Science & Engineering', '+880 1712-345678'),
('Fahim Ahmed', 'fahim@uiu.ac.bd', '$2y$10$3TZsHsL03vh.7k8O8PdaE.MpZr2ma4XHlXseFipWYamjQWNkGJPx6', 'student', 0, '011 221 045', 'Computer Science & Engineering', '+880 1712-345678'),
('Dr. Hasan Sarwar', 'faculty@uiu.ac.bd', '$2y$10$3TZsHsL03vh.7k8O8PdaE.MpZr2ma4XHlXseFipWYamjQWNkGJPx6', 'faculty', 0, 'FAC-1002', 'Department of Computer Science', '+880 1711-223344'),
('Prof. Mahmudul Hasan', 'dean@uiu.ac.bd', '$2y$10$3TZsHsL03vh.7k8O8PdaE.MpZr2ma4XHlXseFipWYamjQWNkGJPx6', 'dean', 0, 'DEAN-1001', 'Faculty of Computer Science', '+880 1711-556677'),
('Ratul Hasan', 'ratul@uiu.ac.bd', '$2y$10$3TZsHsL03vh.7k8O8PdaE.MpZr2ma4XHlXseFipWYamjQWNkGJPx6', 'student', 0, '011 221 099', 'Computer Science & Engineering', '+880 1711-000001'),
('Shakib Khan', 'shakib@uiu.ac.bd', '$2y$10$3TZsHsL03vh.7k8O8PdaE.MpZr2ma4XHlXseFipWYamjQWNkGJPx6', 'faculty', 0, 'FAC-1020', 'Electrical & Electronic Engineering', '+880 1711-000002');

-- ------------------------------------------------------------
-- 2. SEED VENUES
-- ------------------------------------------------------------
INSERT INTO venues (id, name, type, location, quad, building, floor, suite_code, capacity, infrastructure, setup, slots, rating, status, admin_status, image, thumbnail, about, review_count, attendee_email) VALUES
('electrical-lab-4', 'Electrical Lab 4', 'Lab', 'Main Campus, North Wing', 'North Academic Quad', 'Main Campus', 'Floor 3', 'Lab 4', 40, 'Normal', 'Standard Config', '2 Bookings', 4.5, 'Available', 'Active',
 'https://images.unsplash.com/photo-1581092160562-40aa08e78837?w=1000&h=600&fit=crop',
 'https://images.unsplash.com/photo-1581092160562-40aa08e78837?w=400&h=280&fit=crop',
 'Electrical Lab 4 is a fully equipped lab space for circuit design, testing, and hands-on electrical engineering coursework. Located in the North Wing of the Main Campus, it offers individual workstations with power outlets and safety equipment for practical sessions.', 25, 'electrical-lab-4@uiu.ac.bd'),

('uiu-multi-purpose-hall', 'UIU Multipurpose Hall', 'Lecture Hall', 'Ground Floor', 'North Academic Quad', 'Student Union Bldg', 'Floor G', 'Hall A', 250, 'Normal', 'Stage & 4K', '2 Bookings', 4.9, 'Available', 'Active',
 'https://images.unsplash.com/photo-1524178232363-1fb2b075b655?w=1000&h=600&fit=crop',
 'https://images.unsplash.com/photo-1524178232363-1fb2b075b655?w=400&h=280&fit=crop',
 'The UIU Multipurpose Hall serves as the central hub for grand events, cultural performances, and major academic gatherings at the United International University permanent campus. Equipped with advanced sound systems and staging built for live performances and acoustics.', 42, 'multipurpose-hall@uiu.ac.bd'),

('uiu-design-lab', 'UIU Design Lab', 'Lab', 'Main Campus', 'West Wing', 'Bldg 4', 'Floor 1', 'Room 105', 35, 'Normal', 'Hardware Rapid Proto', 'Open Access', 4.6, 'Available', 'Active',
 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=1000&h=600&fit=crop',
 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=400&h=280&fit=crop',
 'UIU Design Lab is built for creative and technical design work, with computer workstations, drawing tablets, and 3D printing tools for prototyping student projects.', 25, 'design-lab@uiu.ac.bd'),

('media-class', 'Media Class', 'Seminar', '8th Floor', 'Science Complex', 'Science Bldg 4B', 'Floor 8', 'Suite 402', 20, 'Normal', 'Network Hybrid Dual', '4 Sessions', 4.3, 'Booked', 'Maintenance',
 'https://images.unsplash.com/photo-1497366216548-37526070297c?w=1000&h=600&fit=crop',
 'https://images.unsplash.com/photo-1497366216548-37526070297c?w=400&h=280&fit=crop',
 'Media Class is a small studio-style room for video production, podcast recording, and media coursework, equipped with cameras, microphones, and editing stations.', 7, 'media-class@uiu.ac.bd'),

('uiu-innovation-hub', 'UIU Innovation Hub', 'Seminar', 'iRIIC, 10th Floor', 'West Wing', 'iRIIC Bldg', 'Floor 10', 'Hub 10', 120, 'Advanced', 'Wi-Fi 6 High Density', '1 Rally', 4.8, 'Available', 'Active',
 'https://images.unsplash.com/photo-1497215728101-856f4ea42174?w=1000&h=600&fit=crop',
 'https://images.unsplash.com/photo-1497215728101-856f4ea42174?w=400&h=280&fit=crop',
 'The UIU Innovation Hub is a space for students to share ideas, work together, and develop new projects at United International University.', 38, 'innovation-hub@uiu.ac.bd'),

('uiu-hall-2', 'UIU Hall-2', 'Lecture Hall', '6th Floor', 'Student Union', 'Student Union Bldg', 'Floor 6', 'Hall 2', 150, 'Normal', 'Stage Lighting', '3 Bookings', 4.4, 'Booked', 'Active',
 'https://images.unsplash.com/photo-1560439514-4e9645039924?w=1000&h=600&fit=crop',
 'https://images.unsplash.com/photo-1560439514-4e9645039924?w=400&h=280&fit=crop',
 'UIU Hall-2 is a large lecture hall on the 6th Floor, suitable for lectures, seminars, and mid-sized events, equipped with a sound system and stage lighting.', 30, 'hall-2@uiu.ac.bd');

-- ------------------------------------------------------------
-- 3. SEED EQUIPMENT
-- ------------------------------------------------------------
INSERT INTO equipment (venue_id, item) VALUES
('electrical-lab-4', 'Power Outlets'), ('electrical-lab-4', 'Lab Computers'), ('electrical-lab-4', 'Tool Kits'), ('electrical-lab-4', 'Safety Gear'),
('uiu-multi-purpose-hall', '4K Projector'), ('uiu-multi-purpose-hall', 'Smart Whiteboard'), ('uiu-multi-purpose-hall', 'Spatial Audio'), ('uiu-multi-purpose-hall', 'Motorized Podium'),
('uiu-design-lab', 'Design Computers'), ('uiu-design-lab', 'Drawing Tablets'), ('uiu-design-lab', '3D Printer'), ('uiu-design-lab', 'Task Lighting'),
('media-class', 'Cameras'), ('media-class', 'Editing Suite'), ('media-class', 'Microphones'), ('media-class', 'Studio Lights'),
('uiu-innovation-hub', 'Projector'), ('uiu-innovation-hub', 'Smart Screens'), ('uiu-innovation-hub', 'Sound System'), ('uiu-innovation-hub', 'High-Speed WiFi'),
('uiu-hall-2', 'Microphones'), ('uiu-hall-2', 'Sound System'), ('uiu-hall-2', 'Projector'), ('uiu-hall-2', 'Stage Lighting');

-- ------------------------------------------------------------
-- 4. SEED SCHEDULE
-- ------------------------------------------------------------
INSERT INTO schedule (venue_id, event_date, title, time_range, host, status) VALUES
('electrical-lab-4', 'Aug 2', 'Circuits to Solutions', '09:00 AM – 11:30 AM', 'Mr. Md. Abid Hossain', 'confirmed'),
('electrical-lab-4', 'Aug 5', 'Learning to Experiment', '02:00 PM – 04:30 PM', 'Mr. Abdullah Ibne Masud Mahi', 'tentative'),
('uiu-multi-purpose-hall', 'Jul 26', 'Intro to Quantum Computing', '09:00 AM – 11:30 AM', 'Prof. Md. Mamun Elahi', 'confirmed'),
('uiu-multi-purpose-hall', 'Jul 27', 'Campus Life to Career', '02:00 PM – 04:30 PM', "Dean's Office", 'tentative'),
('uiu-design-lab', 'Aug 10', 'Concept to Creation', '09:00 AM – 11:30 AM', 'Prof. Md. Al-Sakib Khan Pathan', 'confirmed'),
('uiu-design-lab', 'Aug 11', 'Design to Development', '02:00 PM – 04:30 PM', "Dean's Office", 'tentative'),
('media-class', 'Aug 15', 'Media Skills to Career', '09:00 AM – 11:30 AM', 'Prof. Mario Hirstein', 'confirmed'),
('media-class', 'Aug 16', 'Learning to Broadcasting', '02:00 PM – 04:30 PM', 'Ms. Kazi Karun Nahar Tania', 'tentative'),
('uiu-innovation-hub', 'Jul 27', 'Technology to Entrepreneurship', '02:00 PM – 04:30 PM', "Dean's Office", 'tentative'),
('uiu-innovation-hub', 'Jul 30', 'Research to Real-World Solutions', '09:00 AM – 11:30 AM', 'Prof. Md. Mamun Elahi', 'confirmed'),
('uiu-hall-2', 'Aug 3', 'Learning to Leadership', '09:00 AM – 11:30 AM', 'Prof. Dr. Muhammad Nurul Huda', 'confirmed'),
('uiu-hall-2', 'Aug 4', 'Skills to Success', '02:00 PM – 04:30 PM', 'Prof. Dr. Khondokar Abdullah-Al-Mamun', 'tentative');

-- ------------------------------------------------------------
-- 5. SEED REVIEWS
-- ------------------------------------------------------------
INSERT INTO reviews (venue_id, initials, reviewer_name, time_ago, review_text, rating) VALUES
('electrical-lab-4', 'FR', 'Mr. M. Fahmin Rahman', '4 days ago', 'Excellent lab facilities. The hands-on equipment made our electrical experiments smooth, practical, and engaging.', 5),
('uiu-multi-purpose-hall', 'HS', 'Dr. Hasan Sarwar', '2 days ago', 'Excellent facilities. The hybrid meeting tech worked flawlessly for my guest speaker in Berlin.', 5),
('uiu-design-lab', 'SH', 'Dr. Md. Shorab Hossain', '5 days ago', 'Creative and well-equipped space. The Design Lab provided the perfect environment to turn our ideas into engaging and innovative designs.', 4),
('media-class', 'UA', 'Ms. Umme Ammara', '2 days ago', 'Great creative environment. The media class had everything needed to bring our ideas to life and make every session engaging.', 4),
('uiu-innovation-hub', 'MN', 'Dr. Muhammad Nomani Kabir', '3 days ago', 'Great place for innovation and collaboration. The space made our workshop and project discussions smooth and engaging.', 5),
('uiu-hall-2', 'MI', 'Dr. Md. Motaharul Islam', '2 days ago', 'Spacious and comfortable venue. The setup was perfect for our seminar, making the event smooth and enjoyable for everyone.', 4);

-- ------------------------------------------------------------
-- 6. SEED REQUESTS
-- ------------------------------------------------------------
INSERT INTO requests (user_id, venue_name, venue_location, booking_date, booking_time, purpose, description, attachment, status) VALUES
(2, 'UIU Innovation Hub', 'iRIIC, 10th Floor', '2026-10-12', '02:00 PM – 04:00 PM', 'Club Seminar', 'Robotics and AI Club annual orientation seminar for newly admitted students.', NULL, 'Approved'),
(2, 'UIU Multipurpose Hall', 'Ground Floor', '2026-10-18', '10:00 AM – 04:00 PM', 'Tech Fest Exhibition', 'Grand campus tech fest project exhibition with interactive project showcase booths.', NULL, 'Pending'),
(2, 'UIU Multipurpose Hall', 'Ground Floor', '2026-10-19', '10:00 AM – 01:00 PM', 'Club Seminar', 'Photography club lighting and editing masterclass.', NULL, 'Pending'),
(2, 'UIU Design Lab', 'Main Campus', '2026-10-25', '11:30 AM – 01:30 PM', 'Workshop', 'Prototyping and 3D modeling workshop for engineering seniors.', NULL, 'Approved'),
(2, 'Electrical Lab 4', 'Main Campus, North Wing', '2026-10-30', '09:00 AM – 11:30 AM', 'Coursework', 'Departmental final lab practical exam.', NULL, 'Rejected'),
(5, 'UIU Hall-2', '6th Floor', '2026-10-14', '09:00 AM – 11:00 AM', 'Academic Lecture', 'Senior project symposium.', NULL, 'Pending'),
(6, 'Electrical Lab 4', 'Main Campus, North Wing', '2026-10-07', '02:00 PM – 04:00 PM', 'Seminar / Workshop', 'PCB design workshop for EE students.', NULL, 'Rejected');

-- ------------------------------------------------------------
-- 7. SEED MESSAGES
-- ------------------------------------------------------------
INSERT INTO messages (sender_id, receiver_id, sender_name, text, time_sent, is_read) VALUES
(1, 2, 'Admin - Campus Center', 'Hello Fahim! Your booking request for the Innovation Hub has been received and approved.', '10:38 AM', 1),
(2, 1, 'Fahim Ahmed', 'Hi! Yes, we are expecting around 25 students. We will need the projector and microphone set if possible.', '10:40 AM', 1),
(1, 2, 'Admin - Campus Center', 'Sure, the Innovation Hub is available and the projector is built-in. Multimedia access is enabled for your reservation.', '10:42 AM', 1),
(4, 1, 'Dr. Hasan Sarwar', 'Is there any lecture hall available for our senior project symposium next Wednesday?', 'Yesterday', 0),
(5, 1, 'Ratul Hasan', 'Hello, can I book the hall for a seminar on Oct 14?', '11:05 AM', 0);

-- ------------------------------------------------------------
-- 8. SEED NOTIFICATIONS
-- ------------------------------------------------------------
INSERT INTO notifications (user_id, title, message, type, time_ago, is_read) VALUES
(2, 'Campus-wide Maintenance', 'Main auditorium and GB4 will be closed from Sep 18 to Sep 21 for comprehensive audiovisual and stage lighting upgrades. Alternative halls are open for booking.', 'maintenance', '1 hour ago', 0),
(2, 'Your Booking Request is Accepted', 'Your reservation for UIU Innovation Hub on Oct 12, 2026 has been officially approved by Campus Administration. Hall keys can be collected at GB 101.', 'approval', '2 hours ago', 0),
(2, 'Request Rejected', 'The booking request for Electrical Lab 4 has been rejected due to a scheduled university departmental examination.', 'rejection', '3 hours ago', 1),
(NULL, 'Booking Confirmation', 'Sent to Dr. Aris Thorne for the Biotech Symposium.', 'general', '5 hours ago', 0);

-- ------------------------------------------------------------
-- 9. SEED ANNOUNCEMENTS
-- ------------------------------------------------------------
INSERT INTO announcements (title, content, category, priority, status, status_label, audience, time_text, author, reads_count, delivery, tags) VALUES
('Campus-wide HVAC & Power Grid Maintenance', 'Scheduled electrical load optimization and central heating maintenance impacting North Quad lecture halls, Student Union, and Main Library annex.', 'Maintenance', 'Urgent', 'Active', 'Active / Live', 'All Faculty & Students IC', 'Oct 18, 2026 • 09:30 AM', 'Dr. Aris Thorne (Registrar)', '1,420', '84%', 'Maintenance,Urgent'),
('Booking Confirmation Protocol Updates for Fall 2026', 'Student governance bodies and university-affiliated clubs must submit digital venue requisitions at least 72 hours in advance. Weekend auditoriums now require faculty sponsorship.', 'Booking Policy', 'Normal', 'Active', 'Active / Live', 'Student Orgs & Clubs', 'Oct 15, 2026 • 02:15 PM', 'Admin User (Registrar Office)', '382', '100%', 'Booking Policy,Normal'),
('Severe Weather Warning: Outdoor Venue Closures', 'High precipitation wind advisory issued by state meteorology. Lakeside Amphitheater, Athletics Oval, and Courtyard pavilions will suspend all events.', 'Weather Alert', 'High Priority', 'Scheduled', 'Scheduled: Oct 22 • 06:00 AM', 'Main Campus & Lakeside', 'Push Alert + SMS Enabled', 'Campus Safety Liaison', '0', '0%', 'Weather Alert,High Priority'),
('Biotech Symposium: Auditorium A/V Setup Completed', 'Technical checks complete for hybrid broadcasting in Science Building Auditorium 4B. Presenters can load slide decks 45 minutes ahead of their session.', 'Event Notice', 'Normal', 'Archived', 'Expired / Archived', 'Science & Med Faculty', 'Oct 10, 2026 • Concluded', 'Campus A/V Team', '640', '100%', 'Event Notice,Normal');
