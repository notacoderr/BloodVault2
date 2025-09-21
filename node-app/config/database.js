import { URL } from 'node:url';
import { Sequelize } from 'sequelize';
import dotenv from 'dotenv';

// Load environment variables so the connection can be configured without
// hard-coding secrets in source control. In a production deployment this file
// expects values such as DB_HOST, DB_DATABASE, DB_USERNAME and DB_PASSWORD.
dotenv.config();

function normaliseDialect(value, fallback = 'mysql') {
  if (!value) {
    return fallback;
  }
  const cleaned = value.toString().toLowerCase().replace(/:$/, '');
  if (cleaned === 'postgresql') {
    return 'postgres';
  }
  if (cleaned === 'mysql2') {
    return 'mysql';
  }
  return cleaned;
}

function defaultPortForDialect(dialect) {
  switch (dialect) {
    case 'postgres':
      return 5432;
    case 'mssql':
      return 1433;
    default:
      return 3306;
  }
}

const fallbackDialect = normaliseDialect(process.env.DB_CONNECTION, 'mysql');
const baseConfig = {
  dialect: fallbackDialect,
  host: process.env.DB_HOST || process.env.MYSQLHOST || '127.0.0.1',
  port: Number(process.env.DB_PORT || process.env.MYSQLPORT) || defaultPortForDialect(fallbackDialect),
  database: process.env.DB_DATABASE || process.env.MYSQLDATABASE || 'bloodvault',
  username: process.env.DB_USERNAME || process.env.MYSQLUSER || 'root',
  password: process.env.DB_PASSWORD || process.env.MYSQLPASSWORD || ''
};

const connectionUrl =
  process.env.DATABASE_URL ||
  process.env.DB_URL ||
  process.env.MYSQL_URL ||
  process.env.CLEARDB_DATABASE_URL ||
  process.env.JAWSDB_URL ||
  process.env.JAWSDB_MARIA_URL;

const connectionConfig = { ...baseConfig };
const dialectOptions = {};

if (connectionUrl) {
  try {
    const url = new URL(connectionUrl);
    const urlDialect = normaliseDialect(url.protocol, connectionConfig.dialect);

    connectionConfig.dialect = normaliseDialect(process.env.DB_CONNECTION, urlDialect);
    connectionConfig.host = url.hostname || connectionConfig.host;
    connectionConfig.port = Number(url.port) || connectionConfig.port || defaultPortForDialect(connectionConfig.dialect);
    connectionConfig.database = url.pathname?.replace(/^\//, '') || connectionConfig.database;
    connectionConfig.username = url.username ? decodeURIComponent(url.username) : connectionConfig.username;
    connectionConfig.password = url.password ? decodeURIComponent(url.password) : connectionConfig.password;

    const sslMode = url.searchParams.get('sslmode') || url.searchParams.get('ssl');
    if (sslMode === 'require' || sslMode === 'true') {
      dialectOptions.ssl = { require: true, rejectUnauthorized: false };
    }
  } catch (error) {
    console.warn('Failed to parse database connection string. Falling back to discrete credentials.');
  }
}

const sequelizeOptions = {
  host: connectionConfig.host,
  port: connectionConfig.port,
  dialect: connectionConfig.dialect,
  logging: false,
  define: {
    underscored: false,
    freezeTableName: true,
    timestamps: true
  }
};

if (Object.keys(dialectOptions).length > 0) {
  sequelizeOptions.dialectOptions = dialectOptions;
}

export const sequelize = new Sequelize(
  connectionConfig.database,
  connectionConfig.username,
  connectionConfig.password,
  sequelizeOptions
);

export async function connectDB() {
  await sequelize.authenticate();
  return sequelize;
}

export default sequelize;
