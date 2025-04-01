import { FindService } from './../../../angular/find/src/app/000_core/service/find.service';
import { Component, CUSTOM_ELEMENTS_SCHEMA } from '@angular/core';
import { RouterOutlet } from '@angular/router';
import { SpashPageComponent } from './010_header/spash-page/spash-page.component';
import { NavbarComponent } from './010_header/navbar/navbar.component';
import { HeaderComponent } from './010_header/header/header.component';
import { FooterComponent } from './010_header/footer/footer.component';
import { LibrariesComponent } from './020_find/widget/libraries/libraries.component';
import { LocalStorageService } from './000_core/service/local-storage.service';

@Component({
  selector: 'app-root',
  standalone: true,
  imports: [
    RouterOutlet,
    SpashPageComponent,
    NavbarComponent,
    HeaderComponent,
    FooterComponent,
    LibrariesComponent,
  ],
  schemas: [CUSTOM_ELEMENTS_SCHEMA],
  templateUrl: './app.component.html',
  styleUrl: './app.component.scss',
})
export class AppComponent {
  title = 'findAPP';
  public data: Array<any> | any;
  public libraries: any[] = [];
  public libraryID: string = '';
  public library: any[] = [];

  constructor(
    private findService: FindService, // private router: Router
    private localStorage: LocalStorageService // private localStorage: LocalStorageService
  ) {
    console.log('constructor app component');
  }

  ngOnInit(): void {
    this.libraryID = localStorage.getItem('library') || '';
    if (this.libraryID == '') {
      this.findService.api_post('library', []).subscribe((res) => {
        this.data = res;
        this.libraries = this.data.library;
        console.log(this.data.library);
      });
    } else {
        this.findService
          .api_post('getLibrary/' + this.libraryID, [])
          .subscribe((res) => {
            this.data = res;
            this.libraries = this.data;
            console.log(this.data);
          });
    }
  }
}
