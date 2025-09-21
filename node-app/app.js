import { bootstrap } from './server.js';

bootstrap().catch((error) => {
  console.error('Unable to start BloodVault Node API', error);
  process.exit(1);
});
