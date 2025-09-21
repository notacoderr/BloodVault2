import { Router } from 'express';
import bcrypt from 'bcryptjs';
import jwt from 'jsonwebtoken';

export default function createAuthRoutes({ models, jwtSecret }) {
  const router = Router();
  const { User } = models;

  router.post('/register', async (req, res) => {
    try {
      const { email, password, name, usertype } = req.body;
      const existing = await User.findOne({ where: { email } });
      if (existing) {
        return res.status(409).json({ message: 'Email already in use' });
      }

      const hashedPassword = await bcrypt.hash(password, 10);
      const user = await User.create({ email, password: hashedPassword, name, usertype });
      const token = jwt.sign({ sub: user.id, role: user.usertype }, jwtSecret, { expiresIn: '1h' });

      res.status(201).json({ id: user.id, email: user.email, token });
    } catch (error) {
      console.error('Registration failed', error);
      res.status(500).json({ message: 'Registration failed' });
    }
  });

  router.post('/login', async (req, res) => {
    try {
      const { email, password } = req.body;
      const user = await User.unscoped().findOne({ where: { email } });
      if (!user) {
        return res.status(401).json({ message: 'Invalid credentials' });
      }

      const valid = await bcrypt.compare(password, user.password);
      if (!valid) {
        return res.status(401).json({ message: 'Invalid credentials' });
      }

      const token = jwt.sign({ sub: user.id, role: user.usertype }, jwtSecret, { expiresIn: '1h' });
      res.json({ token, user: { id: user.id, email: user.email, name: user.name, usertype: user.usertype } });
    } catch (error) {
      console.error('Login failed', error);
      res.status(500).json({ message: 'Login failed' });
    }
  });

  return router;
}
