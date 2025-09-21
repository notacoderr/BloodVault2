/**
 * Mirrors 2019_12_14_000001_create_personal_access_tokens_table.
 * @param {import('sequelize').QueryInterface} queryInterface
 * @param {typeof import('sequelize').Sequelize} Sequelize
 */
async function up(queryInterface, Sequelize) {
  await queryInterface.createTable('personal_access_tokens', {
    id: {
      type: Sequelize.BIGINT.UNSIGNED,
      allowNull: false,
      autoIncrement: true,
      primaryKey: true
    },
    tokenable_type: {
      type: Sequelize.STRING(255),
      allowNull: false
    },
    tokenable_id: {
      type: Sequelize.BIGINT.UNSIGNED,
      allowNull: false
    },
    name: {
      type: Sequelize.STRING(255),
      allowNull: false
    },
    token: {
      type: Sequelize.STRING(64),
      allowNull: false,
      unique: true
    },
    abilities: {
      type: Sequelize.TEXT,
      allowNull: true
    },
    last_used_at: {
      type: Sequelize.DATE,
      allowNull: true
    },
    expires_at: {
      type: Sequelize.DATE,
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
  await queryInterface.addIndex('personal_access_tokens', ['tokenable_type', 'tokenable_id']);
  await queryInterface.addIndex('personal_access_tokens', ['token']);
}

async function down(queryInterface) {
  await queryInterface.dropTable('personal_access_tokens');
}

module.exports = { up, down };
