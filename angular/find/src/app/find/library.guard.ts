import { CanActivateFn, Router } from '@angular/router';
import { LibraryApiService } from '../service/Api/library-api.service';

export const libraryGuard: CanActivateFn = (route, state) => {



  //constructor(private LibraryApiService: LibraryApiService) {}
  //console.log(LibraryApiService.selectedLibraries());
  return true
  ;
};
