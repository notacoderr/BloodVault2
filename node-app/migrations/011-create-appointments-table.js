/**
 * Mirrors 2025_08_17_034824_create_appointments_table.
 * @param {import('sequelize').QueryInterface} queryInterface
 * @param {typeof import('sequelize').Sequelize} Sequelize
 */
export async function up(queryInterface, Sequelize) {
  await queryInterface.createTable('appointments', {
    id: {
      type: Sequelize.BIGINT.UNSIGNED,
      allowNull: false,
      autoIncrement: true,
      primaryKey: true
    },
    user_id: {
      type: Sequelize.BIGINT.UNSIGNED,
      allowNull: false,
      references: {
        model: 'users',
        key: 'USER_ID'
      },
      onDelete: 'CASCADE'
    },
    appointment_type: {
      type: Sequelize.STRING(50),
      allowNull: false
    },
    appointment_date: {
      type: Sequelize.DATE,
      allowNull: false
    },
    time_slot: {
      type: Sequelize.STRING(20),
      allowNull: true
    },
    status: {
      type: Sequelize.ENUM('pending', 'confirmed', 'cancelled', 'completed'),
      allowNull: false,
      defaultValue: 'pending'
    },
    notes: {
      type: Sequelize.TEXT,
      allowNull: true
    },
    admin_notes: {
      type: Sequelize.TEXT,
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
  await queryInterface.addIndex('appointments', ['user_id']);
  await queryInterface.addIndex('appointments', ['appointment_date']);
  await queryInterface.addIndex('appointments', ['status']);
  await queryInterface.addIndex('appointments', ['appointment_type']);
}

export async function down(queryInterface) {
  await queryInterface.dropTable('appointments');
}
