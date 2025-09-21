import jwt from 'jsonwebtoken';

export function createAuthHelpers({ jwtSecret }) {
  if (!jwtSecret) {
    throw new Error('JWT secret is required to create authentication helpers.');
  }

  function authenticate(req, res, next) {
    const header = req.headers.authorization;
    if (!header) {
      return res.status(401).json({ message: 'Missing Authorization header' });
    }

    const token = header.replace(/^Bearer\s+/i, '');

    try {
      const payload = jwt.verify(token, jwtSecret);
      req.user = payload;
      next();
    } catch (error) {
      res.status(401).json({ message: 'Invalid token' });
    }
  }

  function isAdminRequest(req) {
    return req.user?.role === 'admin';
  }

  function ensureOwnershipOrAdmin(req, ownerId) {
    return isAdminRequest(req) || req.user?.sub === ownerId;
  }

  function scopedFilter(req, filter = {}) {
    if (isAdminRequest(req)) {
      return filter;
    }
    return { ...filter, userId: req.user?.sub };
  }

  return {
    authenticate,
    isAdminRequest,
    ensureOwnershipOrAdmin,
    scopedFilter
  };
}

export default createAuthHelpers;
