# BloodVault Project Documentation

## System architecture

### Technology stack
- **Backend:** Node.js 18+, Express 4, Sequelize ORM
- **Frontend:** Vanilla JavaScript single-page dashboard served from `node-app/public`
- **Database:** MySQL or MariaDB (relational data), MongoDB for Agenda job storage (optional)
- **Authentication:** JSON Web Tokens (JWT) issued by the Express API
- **Real-time:** Socket.IO broadcasting for live inventory and appointment updates
- **Email:** Nodemailer with SMTP transport
- **Background work:** Agenda scheduled jobs for reminders and notifications

### Application layers
1. **Express API** – exposes REST endpoints for authentication, profile
   management, requests, donations, appointments and inventory operations.
2. **Sequelize models** – map the core domain entities and encapsulate
   relationships, scopes and helper methods.
3. **Job queue** – Agenda definitions trigger email reminders, availability
   broadcasts and other asynchronous workflows.
4. **Browser dashboard** – static assets in `node-app/public` provide the admin
   and donor experience, consuming the same API as external clients.

## Core features

### User management
- Registration and login handled by `/auth/register` and `/auth/login`
- Password hashing with `bcryptjs`
- JWT based sessions with role-aware authorisation (admin vs donor)
- Profile endpoint `/users/me` for account details

### Blood request management
- CRUD endpoints under `/requests`
- Status tracking (`pending`, `approved`, `in_progress`, `fulfilled`, `rejected`)
- Allocation fields for issued units and matching inventory entries
- Email notifications when request state changes

### Donation management
- Endpoints under `/donations`
- Screening information, vital statistics and scheduling data captured in the
  model
- Status transitions broadcast over Socket.IO and delivered via email

### Appointment scheduling
- `/appointments` endpoints for booking, updating and cancelling appointments
- Support for appointment types, locations, notes and status workflow
- Agenda reminder job notifies donors of upcoming slots

### Inventory management
- `/inventory` endpoints expose blood bank stock levels
- Aggregations provide totals by blood type and availability windows
- Socket.IO channel `blood-bank:availability` keeps dashboards in sync

### Email and notifications
- `node-app/services/emailService.js` centralises SMTP configuration
- `node-app/services/notificationService.js` formats transactional emails for
  requests, donations and appointments
- Agenda jobs reuse the same services for background delivery

## Data model overview

| Model          | Purpose                                                      |
|----------------|--------------------------------------------------------------|
| `User`         | Donors and administrators with profile metadata              |
| `BloodRequest` | Requests submitted by users with urgency and fulfilment data |
| `BloodDonation`| Donation appointments and screening outcomes                 |
| `Appointment`  | General purpose scheduling entity for meetings and follow-up |
| `BloodBank`    | Inventory ledger tracking available units and expiry dates   |

Associations mirror the original business logic: requests, donations and
appointments belong to a user, while inventory entries stand alone but influence
request fulfilment workflows.

## API summary

```
GET    /health
POST   /auth/register
POST   /auth/login
GET    /users/me

GET    /requests
POST   /requests
GET    /requests/:id
PATCH  /requests/:id
DELETE /requests/:id

GET    /donations
POST   /donations
PATCH  /donations/:id
DELETE /donations/:id

GET    /appointments
POST   /appointments
PATCH  /appointments/:id
DELETE /appointments/:id

GET    /inventory
POST   /inventory
PATCH  /inventory/:id
DELETE /inventory/:id
```

All protected routes require a bearer token produced during login. Admin-only
operations perform additional role checks inside middleware helpers such as
`isAdminRequest` and `ensureOwnershipOrAdmin`.

## Background jobs

Agenda jobs are registered in `node-app/app.js`:
- `send-email-verification` – delivers sign-up verification links
- `broadcast-blood-availability` – pushes live stock counts over Socket.IO
- `remind-upcoming-appointments` – emails donors about impending appointments

If MongoDB is not configured Agenda runs in memory, which is sufficient for
local development but not for production persistence.

## Real-time channels

Socket.IO namespaces deliver live updates to the dashboard:
- `blood-bank:availability` for inventory refresh
- `request:*` and `appointment:*` events emitted from CRUD handlers

Clients connect automatically via the bundled `app.js` in `node-app/pages` and
update the DOM when messages arrive.

## Security considerations

- Passwords are hashed with `bcryptjs`
- JWT secrets should be long, random strings stored outside source control
- CORS restrictions can be configured through the `APP_ORIGIN` environment
  variable
- Validation is handled inside the request handlers; extend with additional
  checks or a schema validator as needed

## Deployment notes

- Use `npm start` with `NODE_ENV=production` for production deployments
- Configure a process manager (PM2, systemd) to keep the server alive
- Ensure MySQL, MongoDB and SMTP credentials are provisioned via environment
  variables
- Serve the application behind HTTPS when exposed publicly
