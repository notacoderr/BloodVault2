# BloodVault2

BloodVault2 is a JavaScript implementation of the BloodVault blood bank
management platform. The legacy Laravel/PHP stack has been removed in favour of
a Node.js and Express service paired with a lightweight vanilla JavaScript
single-page dashboard.

## Project structure

- `node-app/` – Express API, Sequelize models, Agenda job definitions and the
  static dashboard served to end users.
- `documentation.md` – in-depth architecture and feature notes for the current
  JavaScript stack.
- `setup.md` – step-by-step environment preparation and run instructions.

## Requirements

- Node.js 18 or newer
- npm 9 or newer
- MySQL or MariaDB for the primary data store
- MongoDB (optional) for Agenda job persistence
- An SMTP service (optional) for transactional email delivery

## Quick start

```bash
cd node-app
cp .env.example .env  # adjust credentials and secrets
npm install
npm run dev
```

The development server listens on `http://localhost:4000` by default and serves
both the JSON API and the single-page dashboard. When you are ready to deploy a
production build, run `npm start` instead.

## Database migrations

The Sequelize migration files live in `node-app/migrations`. Install
`sequelize-cli` (either globally or as a dev dependency) to run them against
your database:

```bash
npm install --save-dev sequelize-cli
npx sequelize-cli db:migrate \
  --migrations-path migrations \
  --url "mysql://user:password@localhost:3306/bloodvault"
```

Adjust the connection URL or configure a `.sequelizerc` file if you prefer a
custom setup. The migrations mirror the schema that previously existed in the
Laravel project.

## Scripts

Inside `node-app` the following npm scripts are available:

- `npm run dev` – start the development server with live reload.
- `npm start` – start the server in production mode.
- `npm run lint` – run ESLint across the JavaScript sources.

## Further reading

Refer to `documentation.md` for a breakdown of the domain features, to
`setup.md` for a complete environment checklist, and to `railway.md` for hosted
deployment guidance on Railway.
