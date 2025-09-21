# Railway deployment guide

This guide walks through deploying the BloodVault Node.js service to
[Railway](https://railway.app). The Express API, Socket.IO gateway and dashboard
live in the `node-app` directory of this repository, so Railway must be pointed
at that subfolder when building the service.

## 1. Create a Railway project and service

1. In the Railway dashboard click **New Project → Deploy from GitHub repo** and
   authorize Railway to read your account if prompted.
2. Pick the repository that contains BloodVault2 (your fork or the upstream
   repo). If you already created a generic Node service from the template that
   points at `alphasec/nodejs`, delete that service or disconnect its source so
   the new one can attach to the correct repository.
3. After the repository is selected Railway asks for a root directory. Enter
   `node-app` so the build runs inside the Express project instead of the
   repository root. You can change this later under **Service → Settings → Build
   & Deploy → Root Directory**. If the service still shows `alphasec/nodejs` as
   the deployment source, open the same settings page, click **Disconnect** next
   to the GitHub repository, and then **Connect** to your BloodVault2 fork with
   `node-app` as the root directory.
4. Railway's Nixpacks builder will detect the Node application automatically and
   run `npm install` followed by `npm start`. You do not need to override the
   start command unless you want to enable the development server manually.
5. Set the service health check path to `/health` so Railway can verify the API
   is running.

## 2. Provision backing services

* **MySQL** – Add the managed MySQL plugin. The resulting environment variables
  (`MYSQLHOST`, `MYSQLPORT`, `MYSQLUSER`, `MYSQLPASSWORD`, `MYSQLDATABASE` and
  the generated connection URL) are consumed automatically by
  `node-app/config/database.js`.
* **MongoDB (optional)** – Add the MongoDB plugin if you want Agenda-based
  background jobs. Copy the provided connection string into the `MONGO_URL`
  environment variable.
* **SMTP (optional)** – Configure the `MAIL_*` variables for your preferred mail
  provider or leave them blank to use Nodemailer's stream transport in
  development mode.

## 3. Configure environment variables

Create the following variables on the Railway service ("Variables" tab or via
`railway variables set`):

| Variable | Required | Description |
| --- | --- | --- |
| `APP_URL` | ✅ | Public URL of the deployed API, e.g. `https://<service>.up.railway.app`. |
| `APP_ORIGIN` | ✅ | Allowed front-end origins. Use the same value as `APP_URL` unless the dashboard is hosted elsewhere. Multiple origins can be comma separated. |
| `JWT_SECRET` | ✅ | Random string used to sign authentication tokens. |
| `DATABASE_URL` | ✅ | (Recommended) Paste the MySQL connection string shown in the plugin's "Connect" panel. This allows CLI tooling to reuse the same URL. |
| `MONGO_URL` | Optional | Connection string for the MongoDB plugin if Agenda jobs should persist between restarts. |
| `AGENDA_PROCESS_EVERY` | Optional | Override the Agenda polling cadence (defaults to `30 seconds`). |
| `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_ENCRYPTION`, `MAIL_FROM_ADDRESS` | Optional | SMTP credentials for transactional email delivery. |

Railway automatically injects `MYSQLHOST`, `MYSQLPORT`, `MYSQLUSER`,
`MYSQLPASSWORD` and `MYSQLDATABASE` when the MySQL plugin is attached, so no
additional mapping is required. The application will fall back to those
variables if `DATABASE_URL` is not defined.

A template environment group for Railway might look like:

```
APP_URL=https://your-service.up.railway.app
APP_ORIGIN=https://your-service.up.railway.app
JWT_SECRET=generate-a-long-random-string
DATABASE_URL=mysql://user:password@containers-us-west-123.railway.app:3306/railway
MONGO_URL=
AGENDA_PROCESS_EVERY=30 seconds
MAIL_HOST=
MAIL_PORT=587
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=
MAIL_FROM_ADDRESS=no-reply@bloodvault.local
```

## 4. Run the database migrations

After the service is linked to Railway run the Sequelize migrations against the
managed MySQL instance:

```bash
# Authenticate once if you have not already
railway login

# Link the local checkout to the Railway project
railway link

# Execute the migrations using the environment variables from Railway
railway run --service <service-name> -- npx --yes sequelize-cli db:migrate \
  --migrations-path node-app/migrations \
  --url "$DATABASE_URL"
```

Replace `<service-name>` with the name of the deployed Node service. The
`--yes` flag prevents `npx` from prompting during CI/CD runs. You can also run
this command from the Railway dashboard by starting a one-off shell and executing
`npx sequelize-cli db:migrate --migrations-path migrations --url "$DATABASE_URL"`
from the `node-app` directory.

## 5. Deploy and verify

Trigger a deployment from the Railway dashboard (or push to the connected Git
branch). Once the build completes, Railway will expose the service at
`https://<service>.up.railway.app` (or your custom domain). Visit
`/health` to verify the API responds:

```
https://<service>.up.railway.app/health
```

If you later attach a custom domain remember to update `APP_URL` and
`APP_ORIGIN` accordingly. Socket.IO will automatically use the same origin as
long as CORS stays aligned with these variables.
