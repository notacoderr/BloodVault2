# BloodVault Node.js service

This directory contains the canonical JavaScript implementation of the BloodVault
platform. It exposes the REST API, Socket.IO gateway and single-page dashboard
that replace the original Laravel stack.

## Directory overview

- `server.js` – entry point that boots Express, configures Socket.IO, registers
  routes and initialises Agenda jobs.
- `config/` – database configuration shared by the Sequelize models.
- `models/` – Sequelize model definitions mirroring the BloodVault domain.
- `migrations/` – schema migrations that can be executed with `sequelize-cli`.
- `public/` – static dashboard assets served directly by Express.
- `services/` – reusable modules for email delivery and notification logic.

## Environment variables

Copy the example file and update the placeholders:

```bash
cp .env.example .env
```

Key variables include database credentials (`DB_HOST`, `DB_DATABASE`,
`DB_USERNAME`, `DB_PASSWORD`), JWT secrets (`JWT_SECRET`), optional Agenda
backing store (`MONGO_URL`) and SMTP settings (`MAIL_*`). `APP_ORIGIN` may be set
to a comma-separated list of allowed front-end origins if the dashboard is
hosted separately.

## Installing dependencies

```bash
npm install
```

## Database migrations

Install `sequelize-cli` if it is not already available and run the migrations:

```bash
npm install --save-dev sequelize-cli
npx sequelize-cli db:migrate \
  --migrations-path migrations \
  --url "mysql://user:password@localhost:3306/bloodvault"
```

The migration files export standard `up` and `down` functions so they can also be
integrated into custom deployment tooling.

## Running the server

```bash
npm run dev
```

The server listens on `http://localhost:4000` by default, serving both the API
and the dashboard UI. Use `npm start` to launch in production mode.

## Linting

ESLint is configured for the project. Run it via:

```bash
npm run lint
```

## Additional notes

- Socket.IO broadcasts inventory changes on the `blood-bank:availability`
  channel.
- Agenda jobs send email verification messages and appointment reminders when
  MongoDB is available.
- Nodemailer defaults to a stream transport when SMTP credentials are not set,
  making local development frictionless.
