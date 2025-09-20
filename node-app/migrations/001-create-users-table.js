/**
 * Mirrors the Laravel migration 2025_08_17_034609_create_users_table.php
 * and establishes the users table with the same column definitions.
 * @param {import('sequelize').QueryInterface} queryInterface
 * @param {typeof import('sequelize').Sequelize} Sequelize
 */
export async function up(queryInterface, Sequelize) {
  await queryInterface.createTable('users', {
    USER_ID: {
      type: Sequelize.BIGINT.UNSIGNED,
      allowNull: false,
      autoIncrement: true,
      primaryKey: true
    },
    email: {
      type: Sequelize.STRING(30),
      allowNull: false,
      unique: true
    },
    password: {
      type: Sequelize.STRING(255),
      allowNull: false
    },
    name: {
      type: Sequelize.STRING(50),
      allowNull: true
    },
    dob: {
      type: Sequelize.STRING(10),
      allowNull: true
    },
    sex: {
      type: Sequelize.STRING(10),
      allowNull: true
    },
    address: {
      type: Sequelize.STRING(100),
      allowNull: true
    },
    city: {
      type: Sequelize.STRING(50),
      allowNull: true
    },
    province: {
      type: Sequelize.STRING(50),
      allowNull: true
    },
    contact: {
      type: Sequelize.STRING(11),
      allowNull: true
    },
    bloodtype: {
      type: Sequelize.STRING(4),
      allowNull: true
    },
    usertype: {
      type: Sequelize.STRING(30),
      allowNull: true
    },
    schedule_date: {
      type: Sequelize.STRING(10),
      allowNull: true
    },
    last_donation_date: {
      type: Sequelize.STRING(10),
      allowNull: true
    },
    remember_token: {
      type: Sequelize.STRING(100),
      allowNull: true
    },
    created_at: {
      allowNull: false,
      type: Sequelize.DATE,
      defaultValue: Sequelize.literal('CURRENT_TIMESTAMP')
    },
    updated_at: {
      allowNull: false,
      type: Sequelize.DATE,
      defaultValue: Sequelize.literal('CURRENT_TIMESTAMP')
    }
  });
}

export async function down(queryInterface) {
  await queryInterface.dropTable('users');
}
