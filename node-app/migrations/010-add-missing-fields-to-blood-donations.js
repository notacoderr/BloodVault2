/**
 * Mirrors 2025_08_18_074142_add_missing_fields_to_blood_donations_table.
 * @param {import('sequelize').QueryInterface} queryInterface
 * @param {typeof import('sequelize').Sequelize} Sequelize
 */
async function up(queryInterface, Sequelize) {
  await queryInterface.addColumn('blood_donations', 'admin_notes', {
    type: Sequelize.TEXT,
    allowNull: true
  });
  await queryInterface.addColumn('blood_donations', 'quantity', {
    type: Sequelize.INTEGER,
    allowNull: false,
    defaultValue: 1
  });
  await queryInterface.addColumn('blood_donations', 'screening_status', {
    type: Sequelize.STRING(50),
    allowNull: true
  });
}

async function down(queryInterface) {
  await queryInterface.removeColumn('blood_donations', 'admin_notes');
  await queryInterface.removeColumn('blood_donations', 'quantity');
  await queryInterface.removeColumn('blood_donations', 'screening_status');
}

module.exports = { up, down };
