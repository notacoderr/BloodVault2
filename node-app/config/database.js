import { Sequelize } from 'sequelize';
import dotenv from 'dotenv';

// Load environment variables so the connection can be configured without
// hard-coding secrets in source control. In a production deployment this file
// expects values such as DB_HOST, DB_DATABASE, DB_USERNAME and DB_PASSWORD.
dotenv.config();

const database = process.env.DB_DATABASE || 'bloodvault';
const username = process.env.DB_USERNAME || 'root';
const password = process.env.DB_PASSWORD || '';
const host = process.env.DB_HOST || '127.0.0.1';
const dialect = process.env.DB_CONNECTION || 'mysql';

export const sequelize = new Sequelize(database, username, password, {
  host,
  dialect,
  logging: false,
  define: {
    underscored: false,
    freezeTableName: true,
    timestamps: true
  }
});

export default sequelize;
