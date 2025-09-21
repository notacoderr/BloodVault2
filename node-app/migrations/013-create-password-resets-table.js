/**
 * Mirrors 2014_10_12_100000_create_password_resets_table.
 * @param {import('sequelize').QueryInterface} queryInterface
 * @param {typeof import('sequelize').Sequelize} Sequelize
 */
async function up(queryInterface, Sequelize) {
  await queryInterface.createTable('password_resets', {
    email: {
      type: Sequelize.STRING(255),
      allowNull: false,
      primaryKey: true
    },
    token: {
      type: Sequelize.STRING(255),
      allowNull: false
    },
    created_at: {
      type: Sequelize.DATE,
      allowNull: true
    }
  });
  await queryInterface.addIndex('password_resets', ['email']);
}

async function down(queryInterface) {
  await queryInterface.dropTable('password_resets');
}

module.exports = { up, down };
