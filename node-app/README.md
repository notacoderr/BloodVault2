# BloodVault Node.js Implementation

This directory contains a Node.js translation of the Laravel models and database
schema used by the BloodVault application. It replaces the Eloquent models and
migration files with Sequelize equivalents and exposes a feature-complete
Express application that demonstrates how the same behaviours can be achieved
with a JavaScript stack.

## What's included

- **Sequelize models** for users, blood requests, blood donations, blood bank
  inventory, and appointments with the same relationships, helper methods and
  scopes that exist in the Laravel codebase.
- **Migration scripts** that reproduce the database schema defined by the PHP
  migrations.
- **Express server** (`server.js`) that wires the models together with JWT based
  authentication, Socket.IO real-time updates, Nodemailer powered email
  notifications, and Agenda based job scheduling.
- **Static single-page portal** under `public/` that replaces the PHP Blade
  views with a JavaScript dashboard. It consumes the Node API, surfaces the
  key workflows (requests, donations, appointments, inventory, profile) and is
  served directly by the Express app.
- **Services** for sending transactional emails so that higher level features
  can stay focused on business logic.

To try the Node implementation:

1. Install dependencies

   ```bash
   cd node-app
   npm install
   ```

2. Configure environment variables (database credentials, SMTP settings, JWT
   secret, etc.). You can copy the `.env.example` from the Laravel project or
   create a new `.env` file in this directory.

3. Run the development server

   ```bash
   npm run dev
   ```

   The API listens on port `4000` by default, exposes endpoints for
   authentication, blood requests, donations, appointments and inventory, and
   serves the dashboard UI at `/`. Socket.IO broadcasts appear under the
   `blood-*` and `appointment:*` event channels.

4. Execute migrations with your preferred runner. If you install `sequelize-cli`
   globally you can point it at `node-app/migrations` to rebuild the schema.

This setup keeps the original PHP project intact while offering a reference
implementation of the same domain logic using Node.js.
