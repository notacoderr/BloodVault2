import { Model, DataTypes, Op, fn, col, literal } from 'sequelize';

function isSameDay(dateA, dateB) {
  return (
    dateA.getFullYear() === dateB.getFullYear() &&
    dateA.getMonth() === dateB.getMonth() &&
    dateA.getDate() === dateB.getDate()
  );
}

export class Appointment extends Model {
  getStatusColor() {
    switch (this.status) {
      case 'pending':
        return 'warning';
      case 'confirmed':
        return 'info';
      case 'cancelled':
        return 'danger';
      case 'completed':
        return 'success';
      default:
        return 'secondary';
    }
  }

  isConfirmed() {
    return this.status === 'confirmed';
  }

  isCancelled() {
    return this.status === 'cancelled';
  }

  isToday() {
    if (!this.appointmentDate) {
      return false;
    }
    const appointmentDate = new Date(this.appointmentDate);
    const today = new Date();
    return isSameDay(appointmentDate, today);
  }

  isUpcoming() {
    if (!this.appointmentDate) {
      return false;
    }
    return new Date(this.appointmentDate) > new Date();
  }

  static initModel(sequelize) {
    Appointment.init(
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
        appointmentType: {
          type: DataTypes.STRING(50),
          allowNull: false,
          field: 'appointment_type'
        },
        bloodType: {
          type: DataTypes.STRING(4),
          field: 'blood_type'
        },
        appointmentDate: {
          type: DataTypes.DATE,
          allowNull: false,
          field: 'appointment_date'
        },
        timeSlot: {
          type: DataTypes.STRING(20),
          field: 'time_slot'
        },
        status: {
          type: DataTypes.ENUM('pending', 'confirmed', 'cancelled', 'completed'),
          allowNull: false,
          defaultValue: 'pending'
        },
        notes: {
          type: DataTypes.TEXT
        },
        adminNotes: {
          type: DataTypes.TEXT,
          field: 'admin_notes'
        }
      },
      {
        sequelize,
        tableName: 'appointments',
        modelName: 'Appointment',
        scopes: {
          pending: { where: { status: 'pending' } },
          confirmed: { where: { status: 'confirmed' } },
          cancelled: { where: { status: 'cancelled' } },
          completed: { where: { status: 'completed' } },
          today: {
            where: sequelize.where(fn('DATE', col('appointment_date')), '=', fn('CURDATE'))
          },
          upcoming: {
            where: {
              appointmentDate: {
                [Op.gt]: literal('NOW()')
              }
            }
          }
        }
      }
    );
    return Appointment;
  }

  static associate(models) {
    Appointment.belongsTo(models.User, {
      as: 'user',
      foreignKey: 'userId',
      targetKey: 'id'
    });
  }
}

export default Appointment;
