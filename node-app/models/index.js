import { sequelize } from '../config/database.js';
import User from './user.js';
import BloodRequest from './bloodRequest.js';
import BloodDonation from './bloodDonation.js';
import Appointment from './appointment.js';
import BloodBank from './bloodBank.js';

let models = null;

export function initModels() {
  if (models) {
    return models;
  }

  const initializedModels = {
    User: User.initModel(sequelize),
    BloodRequest: BloodRequest.initModel(sequelize),
    BloodDonation: BloodDonation.initModel(sequelize),
    Appointment: Appointment.initModel(sequelize),
    BloodBank: BloodBank.initModel(sequelize)
  };

  Object.values(initializedModels).forEach((model) => {
    if (typeof model.associate === 'function') {
      model.associate(initializedModels);
    }
  });

  models = initializedModels;
  return models;
}

export function getModels() {
  return initModels();
}

const db = new Proxy(
  {},
  {
    get(_, property) {
      if (property === 'sequelize') {
        return sequelize;
      }
      const currentModels = getModels();
      return currentModels[property];
    }
  }
);

export default db;
