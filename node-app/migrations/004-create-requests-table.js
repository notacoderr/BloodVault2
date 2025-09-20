/**
 * Mirrors 2025_08_17_034727_create_requests_table.
 * @param {import('sequelize').QueryInterface} queryInterface
 * @param {typeof import('sequelize').Sequelize} Sequelize
 */
export async function up(queryInterface, Sequelize) {
  await queryInterface.createTable('requests', {
    RESERVATION_ID: {
      type: Sequelize.BIGINT.UNSIGNED,
      allowNull: false,
      autoIncrement: true,
      primaryKey: true
    },
    request_date: {
      type: Sequelize.STRING(6),
      allowNull: true
    },
    required_blood: {
      type: Sequelize.STRING(6),
      allowNull: true
    },
    quantity: {
      type: Sequelize.INTEGER,
      allowNull: true
    },
    proof: {
      type: Sequelize.BLOB,
      allowNull: true
    },
    status: {
      type: Sequelize.TINYINT,
      allowNull: false,
      defaultValue: 0,
      comment: '-1 = denied, 0 = pending, 1 = approved'
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
  await queryInterface.dropTable('requests');
}
