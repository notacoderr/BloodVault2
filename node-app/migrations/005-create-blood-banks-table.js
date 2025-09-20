/**
 * Mirrors 2025_08_17_034750_create_blood_banks_table.php.
 * @param {import('sequelize').QueryInterface} queryInterface
 * @param {typeof import('sequelize').Sequelize} Sequelize
 */
export async function up(queryInterface, Sequelize) {
  await queryInterface.createTable('blood_banks', {
    STOCK_ID: {
      type: Sequelize.BIGINT.UNSIGNED,
      allowNull: false,
      autoIncrement: true,
      primaryKey: true
    },
    donor: {
      type: Sequelize.BIGINT.UNSIGNED,
      allowNull: false,
      references: {
        model: 'users',
        key: 'USER_ID'
      },
      onDelete: 'CASCADE'
    },
    blood_type: {
      type: Sequelize.STRING(10),
      allowNull: true
    },
    acquisition_date: {
      type: Sequelize.DATE,
      allowNull: true
    },
    expiration_date: {
      type: Sequelize.DATE,
      allowNull: true
    },
    quantity: {
      type: Sequelize.INTEGER,
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
  await queryInterface.addIndex('blood_banks', ['donor']);
  await queryInterface.addIndex('blood_banks', ['blood_type']);
}

export async function down(queryInterface) {
  await queryInterface.dropTable('blood_banks');
}
