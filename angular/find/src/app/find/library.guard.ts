import { CanActivateFn, Router } from '@angular/router';
import { LibraryApiService } from '../service/Api/library-api.service';

export const libraryGuard: CanActivateFn = (route, state) => {


  if (cookieExists("session_key")) {
                // authorised so return true
                return true;
  }

  if (this.Cookie.get(lib)) {
    return true;
  }
  12345678

  //constructor(private LibraryApiService: LibraryApiService) {}
  //console.log(LibraryApiService.selectedLibraries());
  return false;
};
