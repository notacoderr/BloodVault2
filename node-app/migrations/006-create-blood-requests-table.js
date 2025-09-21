/**
 * Mirrors 2025_08_17_034801_create_blood_requests_table.
 * @param {import('sequelize').QueryInterface} queryInterface
 * @param {typeof import('sequelize').Sequelize} Sequelize
 */
async function up(queryInterface, Sequelize) {
  await queryInterface.createTable('blood_requests', {
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
    blood_type: {
      type: Sequelize.STRING(5),
      allowNull: false
    },
    units_needed: {
      type: Sequelize.INTEGER,
      allowNull: false
    },
    urgency: {
      type: Sequelize.ENUM('low', 'medium', 'high', 'critical'),
      allowNull: false,
      defaultValue: 'medium'
    },
    reason: {
      type: Sequelize.TEXT,
      allowNull: true
    },
    hospital: {
      type: Sequelize.STRING(255),
      allowNull: true
    },
    contact_person: {
      type: Sequelize.STRING(255),
      allowNull: true
    },
    contact_number: {
      type: Sequelize.STRING(20),
      allowNull: true
    },
    request_date: {
      type: Sequelize.DATE,
      allowNull: false
    },
    status: {
      type: Sequelize.ENUM('pending', 'approved', 'rejected', 'completed', 'cancelled'),
      allowNull: false,
      defaultValue: 'pending'
    },
    admin_notes: {
      type: Sequelize.TEXT,
      allowNull: true
    },
    blood_available: {
      type: Sequelize.BOOLEAN,
      allowNull: false,
      defaultValue: false
    },
    allocated_units: {
      type: Sequelize.INTEGER,
      allowNull: true,
      defaultValue: 0
    },
    additional_notes: {
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
  await queryInterface.addIndex('blood_requests', ['user_id']);
  await queryInterface.addIndex('blood_requests', ['blood_type']);
  await queryInterface.addIndex('blood_requests', ['status']);
  await queryInterface.addIndex('blood_requests', ['urgency']);
  await queryInterface.addIndex('blood_requests', ['request_date']);
}

async function down(queryInterface) {
  await queryInterface.dropTable('blood_requests');
}

module.exports = { up, down };
