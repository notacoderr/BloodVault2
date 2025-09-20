import { Model, DataTypes } from 'sequelize';

const URGENCY_COLORS = {
  critical: 'danger',
  high: 'warning',
  medium: 'info',
  low: 'success'
};

const STATUS_COLORS = {
  pending: 'warning',
  approved: 'info',
  rejected: 'danger',
  completed: 'success',
  cancelled: 'secondary'
};

export class BloodRequest extends Model {
  getUrgencyColor() {
    return URGENCY_COLORS[this.urgency] || 'secondary';
  }

  getStatusColor() {
    return STATUS_COLORS[this.status] || 'secondary';
  }

  static initModel(sequelize) {
    BloodRequest.init(
      {
        id: {
          type: DataTypes.BIGINT.UNSIGNED,
          autoIncrement: true,
          primaryKey: true
        },
        userId: {
          type: DataTypes.BIGINT.UNSIGNED,
          allowNull: false,
          field: 'user_id'
        },
        bloodType: {
          type: DataTypes.STRING(5),
          allowNull: false,
          field: 'blood_type'
        },
        unitsNeeded: {
          type: DataTypes.INTEGER,
          allowNull: false,
          field: 'units_needed'
        },
        urgency: {
          type: DataTypes.ENUM('low', 'medium', 'high', 'critical'),
          allowNull: false,
          defaultValue: 'medium'
        },
        reason: {
          type: DataTypes.TEXT
        },
        hospital: {
          type: DataTypes.STRING(255)
        },
        contactPerson: {
          type: DataTypes.STRING(255),
          field: 'contact_person'
        },
        contactNumber: {
          type: DataTypes.STRING(20),
          field: 'contact_number'
        },
        requestDate: {
          type: DataTypes.DATE,
          allowNull: false,
          field: 'request_date'
        },
        status: {
          type: DataTypes.ENUM('pending', 'approved', 'rejected', 'completed', 'cancelled'),
          allowNull: false,
          defaultValue: 'pending'
        },
        adminNotes: {
          type: DataTypes.TEXT,
          field: 'admin_notes'
        },
        bloodAvailable: {
          type: DataTypes.BOOLEAN,
          allowNull: false,
          defaultValue: false,
          field: 'blood_available'
        },
        allocatedUnits: {
          type: DataTypes.INTEGER,
          allowNull: true,
          defaultValue: 0,
          field: 'allocated_units'
        },
        additionalNotes: {
          type: DataTypes.TEXT,
          field: 'additional_notes'
        }
      },
      {
        sequelize,
        tableName: 'blood_requests',
        modelName: 'BloodRequest',
        defaultScope: {
          order: [['createdAt', 'DESC']]
        },
        scopes: {
          pending: { where: { status: 'pending' } },
          approved: { where: { status: 'approved' } },
          critical: { where: { urgency: 'critical' } },
          high: { where: { urgency: 'high' } }
        }
      }
    );
    return BloodRequest;
  }

  static associate(models) {
    BloodRequest.belongsTo(models.User, {
      as: 'user',
      foreignKey: 'userId',
      targetKey: 'id'
    });
  }
}

export default BloodRequest;
