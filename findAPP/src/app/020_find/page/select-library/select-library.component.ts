import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Router } from '@angular/router';

@Component({
  selector: 'app-select-library',
  standalone: true,
  imports: [CommonModule, SpashPageComponent],
  templateUrl: './select-library.component.html',
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
    this.libraryID = id;
    this.localStorage.set('library', id);
    this.router.navigate(['/']);
  }

  ngOnInit() {
    console.log('OKK');
    this.findService.api_post('library', []).subscribe((res) => {
      console.log(res);
      this.data = res
      this.libraries = this.data.library;
    });
  }
}
