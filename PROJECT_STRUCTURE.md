# IKEA Inventory Management System

## Project Structure

This project has been reorganized for better maintainability and clarity:

```
IKEA/
├── app/                          # Application Logic
│   ├── Controllers/              # HTTP Controllers
│   │   ├── AuthController.php
│   │   ├── DashboardController.php
│   │   ├── RequestController.php
│   │   └── ...
│   ├── Models/                   # Data Models
│   │   ├── User.php
│   │   ├── Ingredient.php
│   │   ├── Purchase.php
│   │   └── ...
│   └── Utils/                    # Utility Classes
│       ├── Auth.php
│       └── Csrf.php
├── config/                       # Configuration Files
│   └── db.php                   # Database Configuration
├── database/                     # Database Related Files
│   ├── database.sql             # Database Schema
│   └── backup_db.php            # Database Backup Script
├── public/                       # Public Assets (Web Accessible)
│   ├── js/                      # JavaScript Files
│   ├── uploads/                 # File Uploads
│   └── README.md                # Assets Documentation
├── resources/                    # Application Resources
│   ├── header.php               # Main Layout Header
│   ├── footer.php               # Main Layout Footer
│   └── views/                   # View Templates
│       ├── auth/                # Authentication Views
│       ├── dashboard/           # Dashboard Views
│       ├── requests/            # Request Management Views
│       └── ...
├── routes/                       # Route Definitions
│   └── web.php                  # Web Routes
├── backups/                      # Backup Files
├── index.php                    # Application Entry Point
└── README.md                    # Project Documentation
```

## Directory Purposes

### `/app/` - Application Logic
- **Controllers/**: Handle HTTP requests and responses
- **Models/**: Represent data structures and business logic
- **Utils/**: Helper classes and utilities

### `/config/` - Configuration
- Database connections
- Application settings
- Environment configurations

### `/database/` - Database Files
- SQL schema files
- Database scripts and migrations
- Backup utilities

### `/public/` - Public Assets
- Static files accessible via web
- JavaScript, CSS, images
- File uploads directory

### `/resources/` - Application Resources
- **views/**: HTML templates and views
- **header.php**: Main layout header
- **footer.php**: Main layout footer

### `/routes/` - Routing
- URL routing definitions
- Route handlers

## Benefits of This Structure

1. **Clear Separation**: Logic, views, and assets are clearly separated
2. **Scalability**: Easy to add new features and modules
3. **Maintainability**: Related files are grouped together
4. **Security**: Public assets are isolated from application code
5. **Standards**: Follows modern PHP project conventions

## File Path Updates

All file paths have been updated to reflect the new structure:
- Controllers moved from `src/controllers/` to `app/Controllers/`
- Models moved from `src/models/` to `app/Models/`
- Views moved from `src/views/` to `resources/views/`
- Utils moved from `src/utils/` to `app/Utils/`
- Assets moved from `assets/` to `public/`
- Database files moved to `database/`

The application should work exactly the same as before, but with a much cleaner and more organized structure.
