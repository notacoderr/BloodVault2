/**
 * Mirrors 2025_08_17_034701_create_screenings_table.
 * @param {import('sequelize').QueryInterface} queryInterface
 * @param {typeof import('sequelize').Sequelize} Sequelize
 */
async function up(queryInterface, Sequelize) {
  await queryInterface.createTable('screenings', {
    SCREENING_ID: {
      type: Sequelize.BIGINT.UNSIGNED,
      allowNull: false,
      autoIncrement: true,
      primaryKey: true
    },
    usertype: {
      type: Sequelize.STRING(30),
      allowNull: true
    },
    province: {
      type: Sequelize.STRING(30),
      allowNull: true
    },
    contact: {
      type: Sequelize.STRING(30),
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

async function down(queryInterface) {
  await queryInterface.dropTable('screenings');
}

module.exports = { up, down };
