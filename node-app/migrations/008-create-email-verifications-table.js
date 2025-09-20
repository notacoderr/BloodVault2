/**
 * Mirrors 2025_08_17_034808_create_email_verifications_table.php.
 * @param {import('sequelize').QueryInterface} queryInterface
 * @param {typeof import('sequelize').Sequelize} Sequelize
 */
export async function up(queryInterface, Sequelize) {
  await queryInterface.createTable('email_verifications', {
    id: {
      type: Sequelize.BIGINT.UNSIGNED,
      allowNull: false,
      autoIncrement: true,
      primaryKey: true
    },
    email: {
      type: Sequelize.STRING(255),
      allowNull: false
    },
    token: {
      type: Sequelize.STRING(255),
      allowNull: false
    },
    expires_at: {
      type: Sequelize.DATE,
      allowNull: false
    },
    used: {
      type: Sequelize.BOOLEAN,
      allowNull: false,
      defaultValue: false
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
  await queryInterface.addIndex('email_verifications', ['email']);
  await queryInterface.addIndex('email_verifications', ['token']);
  await queryInterface.addIndex('email_verifications', ['expires_at']);
  await queryInterface.addIndex('email_verifications', ['used']);
}

export async function down(queryInterface) {
  await queryInterface.dropTable('email_verifications');
}
