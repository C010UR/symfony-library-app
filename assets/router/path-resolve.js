function routeFallback(profile) {
  if (!profile) {
    return {
      name: 'Login',
    };
  }

  if (profile.roles.includes('ROLE_ADMIN')) {
    return {
      name: 'AdminDashboard',
    };
  }

  if (profile.roles.includes('ROLE_USER')) {
    return {
      name: 'UserDashboard',
    };
  }

  throw new Error('User does not have valid roles.');
}

function isAllowed(profile, routeRoles) {
  if (!routeRoles) {
    return true;
  }

  if (!profile) {
    return false;
  }

  for (const routeRole of routeRoles) {
    const found = profile.roles.find(role => role === routeRole);
    if (found) {
      return true;
    }
  }

  return false;
}

export { routeFallback, isAllowed };
