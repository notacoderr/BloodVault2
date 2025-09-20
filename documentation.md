# LifeVault Project Documentation

## System Architecture

### Technology Stack
- **Backend**: Laravel 9 (PHP 8.0.2+)
- **Frontend**: Blade templates with Vite for asset compilation
- **Database**: MySQL/MariaDB
- **Authentication**: Laravel Sanctum
- **Email**: Laravel Mail with SMTP support

### Core Components
- User Management System
- Blood Donation Management
- Blood Request Management
- Appointment Scheduling
- Blood Inventory Management
- Email Notification System
- Admin Dashboard
- User Dashboard

## User Management System

### User Types
- **Regular Users**: Can request blood, donate blood, and schedule appointments
- **Administrators**: Full system access including user management and system configuration

### User Features
- User registration and authentication
- Email verification system
- Profile management (personal information, contact details, blood type)
- Password change functionality
- Account recovery and password reset

### User Profile Fields
- Name, Date of Birth, Sex
- Address, City, Province
- Contact number
- Blood type
- Last donation date
- Schedule preferences

## Blood Donation Management

### Donation Process
1. **Donor Registration**: Users can register as blood donors
2. **Screening Process**: Health screening questionnaire before donation
3. **Appointment Scheduling**: Book donation appointments
4. **Donation Tracking**: Monitor donation status and history
5. **Cooldown Management**: Track time between donations (minimum 56 days)

### Donation Statuses
- Pending
- Approved
- Completed
- Rejected
- Cancelled

### Screening Features
- Health questionnaire
- Medical history review
- Eligibility assessment
- Risk factor evaluation

## Blood Request Management

### Request Process
1. **Request Creation**: Users can submit blood requests
2. **Blood Type Specification**: Specify required blood type and quantity
3. **Urgency Level**: Set request priority
4. **Status Tracking**: Monitor request approval and fulfillment
5. **Inventory Check**: Automatic availability verification

### Request Features
- Blood type and quantity specification
- Urgency level setting
- Hospital/facility information
- Patient details
- Request status tracking
- Cancellation capability

### Request Statuses
- Pending
- Approved
- In Progress
- Completed
- Rejected
- Cancelled

## Appointment Scheduling System

### Appointment Types
- Blood donation appointments
- Consultation appointments
- Follow-up appointments

### Scheduling Features
- Available time slot selection
- Calendar view interface
- Appointment rescheduling
- Cancellation management
- Reminder notifications

### Appointment Management
- Create new appointments
- Edit existing appointments
- Cancel appointments
- Reschedule appointments
- View appointment history

## Blood Inventory Management

### Inventory Tracking
- Blood type categorization
- Quantity monitoring
- Expiration date tracking
- Storage location management
- Stock level alerts

### Inventory Features
- Real-time stock levels
- Expiring blood alerts
- Low stock notifications
- Blood type distribution
- Inventory reports

### Inventory Reports
- Current stock levels
- Expiring inventory
- Low stock alerts
- Blood type distribution
- Export functionality

## Email Notification System

### Email Types
- Welcome emails for new users
- Email verification
- Appointment confirmations
- Appointment cancellations
- Status update notifications
- Blood request updates
- Donation status changes

### Email Features
- Automated notifications
- Bulk email sending
- Email templates
- SMTP configuration
- Email testing tools

## Admin Dashboard

### Administrative Functions
- User management and monitoring
- Blood request approval and management
- Blood donation processing
- Appointment oversight
- Inventory management
- System statistics and reports

### User Management
- View all users
- Edit user information
- Update user status
- Delete users
- Export user data
- User statistics

### Blood Management
- Approve/reject blood requests
- Process blood donations
- Update donation status
- Manage inventory
- Generate reports

### System Monitoring
- Dashboard statistics
- Real-time updates
- Status counts
- Performance metrics
- System health monitoring

## User Dashboard

### Personal Dashboard
- Blood request history
- Donation history
- Appointment schedule
- Profile information
- Status overview

### User Actions
- Create blood requests
- Schedule donations
- Book appointments
- Update profile
- View notifications

## Database Schema

### Core Tables
- **users**: User accounts and profiles
- **blood_donations**: Blood donation records
- **blood_requests**: Blood request records
- **appointments**: Appointment scheduling
- **blood_banks**: Blood inventory storage
- **email_verifications**: Email verification tokens

### Key Relationships
- Users can donate every 56 days
- Users can submit multiple requests
- Users can schedule multiple appointments
- Blood donations affect inventory levels
- Appointments are linked to users and donations

## Security Features

### Authentication
- Laravel Sanctum for API authentication
- Session-based web authentication
- Password hashing and validation
- Remember me functionality

### Authorization
- Role-based access control
- Admin middleware protection
- Route-level security
- User permission validation

### Data Protection
- CSRF protection
- Input validation and sanitization
- SQL injection prevention
- XSS protection

## API Endpoints

### Public Routes
- Home page
- About page
- User registration
- User login
- Password recovery

### Protected User Routes
- User dashboard
- Profile management
- Blood request creation
- Blood donation scheduling
- Appointment management

### Protected Admin Routes
- Admin dashboard
- User management
- Blood request management
- Blood donation processing
- Inventory management
- Email management

## Frontend Features

### User Interface
- Responsive design
- Modern UI components
- Interactive forms
- Real-time updates
- Mobile-friendly layout

### Asset Management
- Vite for fast development
- CSS and JavaScript compilation
- Asset optimization
- Hot module replacement

## Reporting and Analytics

### Dashboard Statistics
- User counts and trends
- Blood donation statistics
- Request fulfillment rates
- Inventory levels
- Appointment scheduling data

### Export Functionality
- User data export
- Blood request reports
- Donation records
- Inventory reports
- Appointment schedules

## System Requirements

### Server Requirements
- PHP 8.0.2 or higher
- MySQL 5.7+ or MariaDB 10.2+
- Composer for dependency management
- Node.js and npm for frontend assets

### PHP Extensions
- BCMath, Ctype, JSON
- Mbstring, OpenSSL, PDO
- Tokenizer, XML, cURL
- GD, MySQL extensions

## Deployment Considerations

### Development Environment
- Local development server
- Database seeding
- Asset compilation
- Error reporting enabled

### Production Environment
- Web server configuration
- Database optimization
- Asset optimization
- Security hardening
- SSL certificate setup
- Backup procedures

## Maintenance and Updates

### Regular Tasks
- Database backups
- Log file rotation
- Security updates
- Performance monitoring
- User data cleanup

### Update Procedures
- Code deployment
- Database migrations
- Asset compilation
- Cache clearing
- Testing procedures

## Support and Troubleshooting

### Common Issues
- Database connection problems
- Permission errors
- Asset compilation issues
- Email configuration
- Performance optimization

### Debugging Tools
- Laravel logging
- Error reporting
- Database query monitoring
- Performance profiling
- User activity tracking

This documentation provides a comprehensive overview of the LifeVault project, covering all major features, system architecture, and operational procedures.
