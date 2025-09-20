/**
 * Mirrors 2025_08_17_112103_modify_allocated_units_nullable_in_blood_requests_table.
 * The base table already matches the final structure, but this migration is kept
 * to document the intent of the original schema change.
 * @param {import('sequelize').QueryInterface} queryInterface
 * @param {typeof import('sequelize').Sequelize} Sequelize
 */
export async function up(queryInterface, Sequelize) {
  await queryInterface.changeColumn('blood_requests', 'allocated_units', {
    type: Sequelize.INTEGER,
    allowNull: true,
    defaultValue: 0
  });
}

export async function down(queryInterface, Sequelize) {
  await queryInterface.changeColumn('blood_requests', 'allocated_units', {
    type: Sequelize.INTEGER,
    allowNull: false,
    defaultValue: 0
  });
}
