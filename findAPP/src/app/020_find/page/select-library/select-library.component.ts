import { Component } from '@angular/core';
import { FindService } from '../../../010_core/service/find.service';
import { LocalStorageService } from '../../../010_core/service/local-storage.service';
import { Router } from '@angular/router';

@Component({
  selector: 'app-select-library',
  standalone: false,
  templateUrl: './select-library.component.html'
})
export class SelectLibraryComponent {
  public data: Array<any> | any;
  public libraries: Array<any> | any;
  public libraryID: string = '';
  public library: any[] = [];

  constructor(
    private findService: FindService, // private router: Router
    private localStorage: LocalStorageService, // private localStorage: LocalStorageService,
    private router: Router
  ) {}

  setLibrary(id: string) {
    console.log('Library ID: ' + id);
    this.libraryID = id;
    this.localStorage.set('library', id);
    this.router.navigate(['/']);
  }

  ngOnInit() {
    console.log('OKK');
    this.findService.api_post('library', []).subscribe((res) => {
      console.log(res);
      this.data = res;
      this.libraries = this.data.library;
    });
  }
}
