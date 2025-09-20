# Setup guide

Follow these steps to run the JavaScript edition of BloodVault locally.

## 1. Install prerequisites

- Node.js 18+ and npm 9+
- MySQL or MariaDB with a database dedicated to BloodVault
- MongoDB (optional but required if you want Agenda powered background jobs)
- An SMTP server (Mailpit, MailHog, etc.) if you want to test outbound email

## 2. Clone and prepare the repository

```bash
git clone <repo-url>
cd BloodVault2
```

## 3. Configure environment variables

```bash
cd node-app
cp .env.example .env
```

Update the `.env` file with your database credentials, JWT secret and any SMTP
settings you wish to use.

## 4. Install dependencies

```bash
npm install
```

## 5. Set up the database schema

Install `sequelize-cli` if you do not already have it and point it to the
provided migrations:

```bash
npm install --save-dev sequelize-cli
npx sequelize-cli db:migrate \
  --migrations-path migrations \
  --url "mysql://user:password@localhost:3306/bloodvault"
```

Alternatively you can use `sequelize.sync()` in a custom script or integrate the
migration files into your own tooling.

## 6. Run the application

```bash
npm run dev
```

The server listens on `http://localhost:4000` and serves the dashboard from the
same port. Use `npm start` for a production configuration.

## 7. Optional extras

- Run `npm run lint` to verify code quality with ESLint.
- Configure `APP_ORIGIN` in `.env` to lock down CORS if the dashboard is hosted
  on a different domain.
- If MongoDB is available the scheduled tasks defined with Agenda will persist
  across restarts.
