import { Model, DataTypes } from 'sequelize';

const COOLDOWN_DAYS = 56;
const DAY_IN_MS = 24 * 60 * 60 * 1000;

function addDays(date, days) {
  return new Date(date.getTime() + days * DAY_IN_MS);
}

export class BloodDonation extends Model {
  getStatusColor() {
    switch (this.status) {
      case 'pending':
        return 'warning';
      case 'approved':
        return 'info';
      case 'completed':
        return 'success';
      case 'rejected':
        return 'danger';
      default:
        return 'secondary';
    }
  }

  isEligible() {
    return this.status === 'approved';
  }

  isCompleted() {
    return this.status === 'completed';
  }

  static async canUserDonate(userId) {
    const lastCompletedDonation = await this.findOne({
      where: {
        userId,
        status: 'completed'
      },
      order: [['donationDate', 'DESC']]
    });

    if (!lastCompletedDonation) {
      return true;
    }

    const nextEligibleDate = addDays(lastCompletedDonation.donationDate, COOLDOWN_DAYS);
    return new Date() >= nextEligibleDate;
  }

  static async getNextEligibleDate(userId) {
    const lastCompletedDonation = await this.findOne({
      where: {
        userId,
        status: 'completed'
      },
      order: [['donationDate', 'DESC']]
    });

    if (!lastCompletedDonation) {
      return null;
    }

    return addDays(lastCompletedDonation.donationDate, COOLDOWN_DAYS);
  }

  static async getRemainingCooldownDays(userId) {
    const nextEligibleDate = await this.getNextEligibleDate(userId);
    if (!nextEligibleDate) {
      return 0;
    }

    const diff = Math.ceil((nextEligibleDate.getTime() - Date.now()) / DAY_IN_MS);
    return Math.max(0, diff);
  }

  static initModel(sequelize) {
    BloodDonation.init(
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
        donorName: {
          type: DataTypes.STRING(255),
          allowNull: false,
          field: 'donor_name'
        },
        donorEmail: {
          type: DataTypes.STRING(255),
          allowNull: false,
          field: 'donor_email'
        },
        bloodType: {
          type: DataTypes.STRING(10),
          allowNull: false,
          field: 'blood_type'
        },
        donationDate: {
          type: DataTypes.DATE,
          allowNull: false,
          field: 'donation_date'
        },
        quantity: {
          type: DataTypes.INTEGER,
          allowNull: false,
          defaultValue: 1
        },
        screeningStatus: {
          type: DataTypes.STRING(50),
          field: 'screening_status'
        },
        status: {
          type: DataTypes.ENUM('pending', 'approved', 'completed', 'rejected'),
          allowNull: false,
          defaultValue: 'pending'
        },
        screeningAnswers: {
          type: DataTypes.TEXT,
          field: 'screening_answers'
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
        tableName: 'blood_donations',
        modelName: 'BloodDonation',
        scopes: {
          pending: { where: { status: 'pending' } },
          approved: { where: { status: 'approved' } },
          completed: { where: { status: 'completed' } },
          rejected: { where: { status: 'rejected' } }
        }
      }
    );
    return BloodDonation;
  }

  static associate(models) {
    BloodDonation.belongsTo(models.User, {
      as: 'user',
      foreignKey: 'userId',
      targetKey: 'id'
    });
  }
}

export default BloodDonation;
