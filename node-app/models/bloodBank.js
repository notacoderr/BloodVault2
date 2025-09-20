import { Model, DataTypes, Op, literal } from 'sequelize';

const STATUS_TEXT = {
  1: 'Approved',
  0: 'Pending',
  '-1': 'Denied'
};

const STATUS_COLOR = {
  1: 'success',
  0: 'warning',
  '-1': 'danger'
};

export class BloodBank extends Model {
  isAvailable() {
    return (
      this.status === 1 &&
      this.expirationDate &&
      this.expirationDate > new Date() &&
      this.quantity > 0
    );
  }

  isExpired() {
    return this.expirationDate ? this.expirationDate < new Date() : false;
  }

  isApproved() {
    return this.status === 1;
  }

  getStatusText() {
    return STATUS_TEXT[this.status] || 'Unknown';
  }

  getStatusColor() {
    return STATUS_COLOR[this.status] || 'secondary';
  }

  static initModel(sequelize) {
    BloodBank.init(
      {
        id: {
          type: DataTypes.BIGINT.UNSIGNED,
          autoIncrement: true,
          primaryKey: true,
          field: 'STOCK_ID'
        },
        donorId: {
          type: DataTypes.BIGINT.UNSIGNED,
          allowNull: false,
          field: 'donor'
        },
        bloodType: {
          type: DataTypes.STRING(10),
          field: 'blood_type'
        },
        acquisitionDate: {
          type: DataTypes.DATE,
          field: 'acquisition_date'
        },
        expirationDate: {
          type: DataTypes.DATE,
          field: 'expiration_date'
        },
        quantity: {
          type: DataTypes.INTEGER
        },
        status: {
          type: DataTypes.TINYINT,
          defaultValue: 0
        }
      },
      {
        sequelize,
        tableName: 'blood_banks',
        modelName: 'BloodBank',
        scopes: {
          approved: { where: { status: 1 } },
          pending: { where: { status: 0 } },
          denied: { where: { status: -1 } },
          available: {
            where: {
              status: 1,
              expirationDate: { [Op.gt]: literal('NOW()') },
              quantity: { [Op.gt]: 0 }
            }
          },
          expired: {
            where: {
              expirationDate: { [Op.lt]: literal('NOW()') }
            }
          }
        }
      }
    );
    return BloodBank;
  }

  static associate(models) {
    BloodBank.belongsTo(models.User, {
      as: 'donor',
      foreignKey: 'donorId',
      targetKey: 'id'
    });
  }
}

export default BloodBank;
