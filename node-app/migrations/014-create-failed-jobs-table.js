/**
 * Mirrors 2019_08_19_000000_create_failed_jobs_table.php.
 * @param {import('sequelize').QueryInterface} queryInterface
 * @param {typeof import('sequelize').Sequelize} Sequelize
 */
export async function up(queryInterface, Sequelize) {
  await queryInterface.createTable('failed_jobs', {
    id: {
      type: Sequelize.BIGINT.UNSIGNED,
      allowNull: false,
      autoIncrement: true,
      primaryKey: true
    },
    uuid: {
      type: Sequelize.STRING(255),
      allowNull: false,
      unique: true
    },
    connection: {
      type: Sequelize.TEXT,
      allowNull: false
    },
    queue: {
      type: Sequelize.TEXT,
      allowNull: false
    },
    payload: {
      type: Sequelize.TEXT('long'),
      allowNull: false
    },
    exception: {
      type: Sequelize.TEXT('long'),
      allowNull: false
    },
    failed_at: {
      type: Sequelize.DATE,
      allowNull: false,
      defaultValue: Sequelize.literal('CURRENT_TIMESTAMP')
    }
  });
}

export async function down(queryInterface) {
  await queryInterface.dropTable('failed_jobs');
}
