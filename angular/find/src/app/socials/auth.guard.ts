import { CanActivateFn, Router } from '@angular/router';

export const authGuard: CanActivateFn = (route, state) => {

  /*
  const token = windows.localSotrage.getItem('token');
  if (token) {
    return true;
  } else {
    return false;
  }
  */
  return true;
};
