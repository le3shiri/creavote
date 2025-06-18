# Creavote

A modern, collaborative platform for creative offer-based design challenges. Users can create offers, submit designs, vote on submissions, and interact with the creative community. Built with PHP, MySQL, and Tailwind CSS for a seamless and interactive experience.

---

## Table of Contents
- [Features](#features)
- [Project Structure](#project-structure)
- [Installation](#installation)
- [Usage](#usage)
- [Core Functionalities](#core-functionalities)
- [Security](#security)
- [Customization](#customization)
- [Credits](#credits)
- [License](#license)

---

## Features

- **User Authentication**: Secure sign up, login, and session management.
- **Offer System**: Users can create, browse, and apply to design offers (projects).
- **Design Submission**: Upload images or videos as entries for offers.
- **Voting System**: Community-driven voting (1-10) on each design, with intuitive hover-to-vote UI.
- **Commenting**: Threaded comments on each design, submitted via AJAX for instant feedback.
- **Profile Pages**: View user profiles, submitted designs, and saved designs.
- **Sidebar Navigation**: Responsive, with active page highlighting for better UX.
- **Notifications**: Real-time notifications for relevant user activity.
- **Modern UI**: Built with Tailwind CSS for a clean, responsive, and accessible interface.
- **Security**: Uses prepared statements for all DB queries, session checks, and file validation.

---

## Project Structure

```
Creavote/
├── assets/                # Static assets (images, logos, CSS)
├── config/                # Configuration files (DB connection, etc.)
├── controllers/           # PHP controllers for business logic
├── sql/                   # Database schema and sample data
├── uploads/               # Uploaded user files (images, videos, profile pics)
├── views/                 # All PHP view files (pages and components)
│   ├── home.php           # Main feed (designs)
│   ├── offers.php         # List of offers
│   ├── apply-offer.php    # Submit a design for an offer
│   ├── profile.php        # User profile page
│   ├── base.php           # Layout & sidebar
│   ├── ...                # Other pages (login, signup, notifications, etc.)
├── README.md              # Project documentation
└── ...
```

---

## Installation

1. **Clone the repository:**
   ```bash
   git clone https://github.com/le3shiri/creavote.git
   ```
2. **Set up your web server:**
   - Use XAMPP, WAMP, or similar (PHP 7.4+ recommended).
   - Place the project in your `htdocs` or web root directory.
3. **Database setup:**
   - Import the SQL schema from `sql/` into your MySQL database.
   - Update `config/db.php` with your DB credentials.
4. **File permissions:**
   - Ensure `uploads/` is writable by the web server for file uploads.
5. **Install dependencies:**
   - Tailwind CSS is included via CDN or build process (see `assets/`).
   - No Composer/NPM dependencies by default.
6. **Start the server:**
   - Visit `http://localhost/Creavote/views/home.php` in your browser.

---

## Usage

- **Sign up** for a new account or log in.
- **Browse offers** and view details.
- **Apply to offers** by uploading an image or video.
- **Vote** on designs by hovering over the blue vote icon and selecting a score (1-10).
- **Comment** on designs and interact with other users.
- **Visit profiles** to see designer stats and their work.
- **Receive notifications** for activity relevant to you.

---

## Core Functionalities

### 1. **Authentication & Security**
- Session-based login/logout.
- User registration with validation.
- All sensitive pages check for active session.

### 2. **Offer Management**
- Create, edit, and view offers.
- Offers display tags, deadlines, budget, and description.

### 3. **Design Submission**
- Apply to an offer by uploading an image or video.
- Supported formats: `.jpg`, `.jpeg`, `.png`, `.gif`, `.webp`, `.mp4`, `.webm`, `.mov`.
- Files are validated for type and extension.

### 4. **Design Feed (Home Page)**
- Shows all submitted designs (images only, not clickable).
- Each card displays the design, designer info, voting, and comments.
- Voting UI is only shown when hovering the vote icon (not the image).

### 5. **Voting System**
- Users can vote 1-10 on each design (once per design).
- Hover over the blue vote icon to reveal voting options.
- Votes are submitted via AJAX for a smooth experience.

### 6. **Commenting**
- Users can leave comments on any design.
- Comments are loaded and submitted asynchronously.

### 7. **Profile Pages**
- View your own and others' profiles.
- See submitted and saved designs.
- Edit your profile and settings.

### 8. **Sidebar Navigation**
- Sidebar highlights the current page for easy navigation.
- Profile link is included and highlights when active.

### 9. **Notifications**
- Real-time notification badge for new activity.
- View all notifications on the notifications page.

---

## Security
- **Sessions**: All sensitive pages require user authentication.
- **Database**: All queries use prepared statements (PDO) to prevent SQL injection.
- **File Uploads**: MIME type and extension validation for all uploads.
- **No CSRF protection yet**: Recommended for production.

---

## Customization
- **Styling**: Easily adjustable via Tailwind CSS classes in the views.
- **Extending Functionality**: Add new pages to `views/`, new logic to `controllers/`.
- **Database**: Modify the schema in `sql/` as needed for new features.

---

## Credits
- **Lead Developer**: le3shiri
- **UI/UX**: Tailwind CSS
- **Icons**: [Heroicons](https://heroicons.com/)
- **Frameworks**: PHP, MySQL

---

## License

This project is licensed under the MIT License. See `LICENSE` for details.

---

**Enjoy building and collaborating with Creavote!**

For questions or contributions, open an issue or pull request on GitHub.
