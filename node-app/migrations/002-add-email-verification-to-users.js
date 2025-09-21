/**
 * Adds the email verification related columns mirroring
 * 2025_08_17_050232_add_email_verification_to_users_table.
 * @param {import('sequelize').QueryInterface} queryInterface
 * @param {typeof import('sequelize').Sequelize} Sequelize
 */
async function up(queryInterface, Sequelize) {
  await queryInterface.addColumn('users', 'email_verified_at', {
    type: Sequelize.DATE,
    allowNull: true
  });
  await queryInterface.addColumn('users', 'email_verification_token', {
    type: Sequelize.STRING(255),
    allowNull: true,
    unique: true
  });
  await queryInterface.addColumn('users', 'is_verified', {
    type: Sequelize.BOOLEAN,
    allowNull: false,
    defaultValue: false
  });
}

async function down(queryInterface) {
  await queryInterface.removeColumn('users', 'email_verified_at');
  await queryInterface.removeColumn('users', 'email_verification_token');
  await queryInterface.removeColumn('users', 'is_verified');
}

module.exports = { up, down };
