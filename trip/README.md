# Travel Journey Sharing Website (HTML Version)

A static HTML + JavaScript version of the travel journey sharing system, simulating dynamic features with client-side scripting.

## Project Description

This is an HTML version of the travel journey sharing platform, using JavaScript for interactivity and localStorage for session simulation. It includes all the pages and features from the PHP version, but runs entirely in the browser.

## Features

- User registration and login simulation with localStorage
- Journey creation, editing, and deletion (simulated)
- Interactive maps using Leaflet.js
- Photo and video galleries
- Comments and likes (client-side)
- User preferences
- Memory matching game
- Responsive design

## Files and Purposes

### Core HTML Pages
- `index.html` - Home page
- `register.html` - Registration form
- `login.html` - Login form
- `dashboard.html` - User dashboard
- `create_journey.html` - Create journey form
- `journey_details.html` - Journey details with map
- `edit_journey.html` - Edit journey form
- `delete_journey.html` - Delete confirmation
- `logout.html` - Logout script

### Additional Pages
- `about.html` - About page
- `contact.html` - Contact form
- `gallery.html` - Photo gallery
- `video_page.html` - Video gallery
- `map_overview.html` - Map overview
- `video_intro.html` - Introduction video
- `preference.html` - User preferences
- `game.html` - Memory game

### Assets
- `style.css` - Global CSS styles
- `script.js` - Global JavaScript functions

### Directory
- `uploads/` - Directory for sample media files (in real deployment, would be served from server)

## Technologies Used

- **Frontend**: HTML5, CSS3, JavaScript
- **Mapping**: Leaflet.js
- **Storage**: localStorage for session simulation
- **Interactivity**: Vanilla JavaScript

## Installation

1. Open `index.html` in a web browser
2. For login, use username: admin, password: password
3. All interactions are simulated client-side

## Default Login Credentials

- Username: admin / Password: password

## Notes

- This is a static simulation of the dynamic PHP version
- Data is not persisted across browser sessions
- File uploads are simulated (no actual server-side processing)
- Maps use sample coordinates and markers
- All forms use JavaScript validation and simulation