# VenueVibe-Project
# VenueVibe — Campus Venue Booking System

**VenueVibe** is a web-based campus venue booking system designed to simplify how students, faculty members, and administrators manage university spaces. It provides a centralized platform for discovering venues, submitting booking requests, tracking reservations, and managing campus facilities.

## Features

### User Portal

- **Landing Page:** Introduces the platform and its key features.
- **User Authentication:** Login and signup functionality.
- **Venue Browsing:** Explore available campus venues and their details.
- **Booking Management:** Submit and manage venue booking requests.
- **Messages and Notifications:** Stay updated on booking activities and announcements.
- **User Dashboard:** Access relevant information and booking-related features in one place.

### Admin Portal

- **Admin Dashboard:** Manage campus venue booking activities.
- **Venue Management:** Maintain venue information and availability.
- **Booking Requests:** Review and manage submitted booking requests.
- **Announcements:** Publish updates and important information for users.
- **Session-Based Access Control:** Restrict administrative pages to authorized administrators.

## Technologies Used

| Technology | Purpose                              |
| ---------- | ------------------------------------ |
| HTML5      | Web page structure                   |
| CSS3       | Styling and responsive layouts       |
| JavaScript | Client-side interactions             |
| PHP        | Server-side logic and authentication |
| MySQL      | Database management                  |
| XAMPP      | Local development environment        |

## Project Structure

```text
venuevibe-naive/
├── User/
│   ├── LandingPage.php
│   ├── login.php
│   ├── signup.php
│   ├── user.php
│   ├── admin.php
│   ├── config.php
│   ├── create_database.sql
│   ├── insert_data.sql
│   └── ...
├── Admin/
│   ├── index.php
│   └── ...
└── README.md
```

*Note: The directory listing above highlights key files. Other project files are omitted for brevity.*

## Installation and Setup

### Prerequisites

- [XAMPP](https://www.apachefriends.org/) with Apache, MySQL, and PHP
- A modern web browser
- Git (optional, for cloning the repository)

### 1. Clone the Repository

Open a terminal and run:

```bash
git clone YOUR_GITHUB_REPOSITORY_URL
```

Replace `YOUR_GITHUB_REPOSITORY_URL` with your actual GitHub repository URL.

Alternatively, download the repository as a ZIP file and extract it.

### 2. Move the Project to XAMPP

Copy the project folder into your XAMPP `htdocs` directory.

**Windows**

```text
C:\xampp\htdocs\venuevibe-naive\
```

**Linux**

```text
/opt/lampp/htdocs/venuevibe-naive/
```

**macOS**

```text
/Applications/XAMPP/htdocs/venuevibe-naive/
```

Keep the folder names `User` and `Admin` unchanged.

### 3. Start Apache and MySQL

1. Open the XAMPP Control Panel.
2. Start **Apache**.
3. Start **MySQL**.

### 4. Configure the Database

1. Open [phpMyAdmin](http://localhost/phpmyadmin/).
2. Select the **Import** tab.
3. Import `User/create_database.sql`.
4. Import `User/insert_data.sql` after the database schema has been created.
5. Check the database credentials in `User/config.php`.

The default XAMPP configuration commonly uses `root` with an empty password, but your settings may differ.

### 5. Verify the Setup

If the project includes the setup checker, open:

```text
http://localhost/venuevibe-naive/User/check-setup.php
```

Resolve any reported errors before proceeding.

### 6. Run VenueVibe

Open the landing page in your browser:

**[http://localhost/venuevibe-naive/User/LandingPage.php](http://localhost/venuevibe-naive/User/LandingPage.php)**

## Authentication and Navigation

VenueVibe provides a shared login page for regular users and administrators.

| Page                   | Description                        |
| ---------------------- | ---------------------------------- |
| `User/LandingPage.php` | Main landing page                  |
| `User/login.php`       | Login for users and administrators |
| `User/signup.php`      | User registration                  |
| `User/user.php`        | User dashboard                     |
| `User/admin.php`       | Admin access verification          |
| `Admin/index.php`      | Admin dashboard                    |

After successful authentication, regular users are directed to the user dashboard, while authorized administrators are directed to the admin portal.

## Demo Accounts

The following accounts are provided by the project's sample data, if imported successfully.

| Role    | Email               | Password      |
| ------- | ------------------- | ------------- |
| User    | `student@uiu.ac.bd` | `password123` |
| Admin   | `admin@uiu.ac.bd`   | `password123` |
| Faculty | `faculty@uiu.ac.bd` | `password123` |

Select the appropriate role on the login page. An account without administrator privileges cannot access the admin portal.

**Security note:** These are development demo credentials. Do not use them in a production deployment.

## Database

The MySQL database is named `venuevibe` and is initialized through the supplied SQL files.

- `create_database.sql` — creates the database schema and tables.
- `insert_data.sql` — inserts sample records for testing.

The database supports the project's venue booking workflow and related features, including users, venues, booking requests, messages, notifications, and announcements.

## Troubleshooting

- **Database connection error:** Ensure MySQL is running and verify `User/config.php`.
- **Missing tables:** Import the SQL files in the correct order.
- **Admin page returns 404:** Confirm that the `Admin` folder is next to the `User` folder and that the capitalization is correct.
- **Blank page or PHP error:** Check the XAMPP Apache error log.
- **Login or session problems:** Enable browser cookies and access the site through `http://localhost/`, not by opening PHP files directly.

## Project Purpose

VenueVibe aims to make campus space booking more organized, accessible, and efficient by bringing venue discovery, reservation requests, and administrative management into a single web application.

## Contributors

VenueVibe was developed as a collaborative project. Contributors worked on different parts of the system, including the user interface, authentication, dashboard functionality, and administration features.

## License

This project was developed for educational purposes. Add a license to the repository if you intend to specify how others may use, modify, or distribute the code.

