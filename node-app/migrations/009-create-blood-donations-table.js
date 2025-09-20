/**
 * Mirrors 2025_08_17_034816_create_blood_donations_table with the later
 * additions from 2025_08_18_074142_add_missing_fields_to_blood_donations_table
 * captured in a follow-up migration.
 * @param {import('sequelize').QueryInterface} queryInterface
 * @param {typeof import('sequelize').Sequelize} Sequelize
 */
export async function up(queryInterface, Sequelize) {
  await queryInterface.createTable('blood_donations', {
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
    donor_name: {
      type: Sequelize.STRING(255),
      allowNull: false
    },
    donor_email: {
      type: Sequelize.STRING(255),
      allowNull: false
    },
    blood_type: {
      type: Sequelize.STRING(10),
      allowNull: false
    },
    donation_date: {
      type: Sequelize.DATE,
      allowNull: false
    },
    status: {
      type: Sequelize.ENUM('pending', 'approved', 'completed', 'rejected'),
      allowNull: false,
      defaultValue: 'pending'
    },
    screening_answers: {
      type: Sequelize.TEXT,
      allowNull: true
    },
    notes: {
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
  await queryInterface.addIndex('blood_donations', ['user_id']);
  await queryInterface.addIndex('blood_donations', ['status']);
  await queryInterface.addIndex('blood_donations', ['donation_date']);
}

export async function down(queryInterface) {
  await queryInterface.dropTable('blood_donations');
}
