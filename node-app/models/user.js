import crypto from 'node:crypto';
import { Model, DataTypes } from 'sequelize';

/**
 * Sequelize model that mirrors App\\Models\\User from Laravel.
 */
export class User extends Model {
  isAdmin() {
    return this.usertype === 'admin';
  }

  isDonor() {
    return this.usertype === 'donor';
  }

  isRequester() {
    return this.usertype === 'requester';
  }

  isEmailVerified() {
    return this.emailVerifiedAt !== null;
  }

  async generateEmailVerificationToken() {
    const token = crypto.randomBytes(32).toString('hex');
    this.emailVerificationToken = token;
    await this.save();
    return token;
  }

  async markEmailAsVerified() {
    this.emailVerifiedAt = new Date();
    this.isVerified = true;
    this.emailVerificationToken = null;
    await this.save();
  }

  bloodRequests(options = {}) {
    return this.getBloodRequests(options);
  }

  bloodDonations(options = {}) {
    return this.getBloodDonations(options);
  }

  appointments(options = {}) {
    return this.getAppointments(options);
  }

  static initModel(sequelize) {
    User.init(
      {
        id: {
          type: DataTypes.BIGINT.UNSIGNED,
          autoIncrement: true,
          primaryKey: true,
          field: 'USER_ID'
        },
        email: {
          type: DataTypes.STRING(30),
          allowNull: false,
          unique: true
        },
        password: {
          type: DataTypes.STRING(255),
          allowNull: false
        },
        name: {
          type: DataTypes.STRING(50)
        },
        dob: {
          type: DataTypes.STRING(10)
        },
        sex: {
          type: DataTypes.STRING(10)
        },
        address: {
          type: DataTypes.STRING(100)
        },
        city: {
          type: DataTypes.STRING(50)
        },
        province: {
          type: DataTypes.STRING(50)
        },
        contact: {
          type: DataTypes.STRING(11)
        },
        bloodtype: {
          type: DataTypes.STRING(4)
        },
        usertype: {
          type: DataTypes.STRING(30)
        },
        scheduleDate: {
          type: DataTypes.STRING(10),
          field: 'schedule_date'
        },
        lastDonationDate: {
          type: DataTypes.STRING(10),
          field: 'last_donation_date'
        },
        rememberToken: {
          type: DataTypes.STRING(100),
          field: 'remember_token'
        },
        emailVerifiedAt: {
          type: DataTypes.DATE,
          field: 'email_verified_at'
        },
        emailVerificationToken: {
          type: DataTypes.STRING(255),
          field: 'email_verification_token'
        },
        isVerified: {
          type: DataTypes.BOOLEAN,
          field: 'is_verified',
          defaultValue: false
        }
      },
      {
        sequelize,
        tableName: 'users',
        modelName: 'User',
        underscored: false,
        defaultScope: {
          attributes: { exclude: ['password', 'rememberToken'] }
        }
      }
    );
    return User;
  }

  static associate(models) {
    User.hasMany(models.BloodRequest, {
      as: 'bloodRequests',
      foreignKey: 'userId',
      sourceKey: 'id'
    });
    User.hasMany(models.BloodDonation, {
      as: 'bloodDonations',
      foreignKey: 'userId',
      sourceKey: 'id'
    });
    User.hasMany(models.Appointment, {
      as: 'appointments',
      foreignKey: 'userId',
      sourceKey: 'id'
    });
    User.hasMany(models.BloodBank, {
      as: 'bloodBankStock',
      foreignKey: 'donorId',
      sourceKey: 'id'
    });
  }
}

export default User;
