/**
 * Mirrors 2025_08_17_055105_add_blood_type_to_appointments_table.
 * @param {import('sequelize').QueryInterface} queryInterface
 * @param {typeof import('sequelize').Sequelize} Sequelize
 */
async function up(queryInterface, Sequelize) {
  await queryInterface.addColumn('appointments', 'blood_type', {
    type: Sequelize.STRING(4),
    allowNull: true
  });
}

async function down(queryInterface) {
  await queryInterface.removeColumn('appointments', 'blood_type');
}

module.exports = { up, down };
