import { FindService } from './../../../angular/find/src/app/000_core/service/find.service';
import { Component, CUSTOM_ELEMENTS_SCHEMA } from '@angular/core';
import { RouterOutlet } from '@angular/router';
import { SpashPageComponent } from './010_header/spash-page/spash-page.component';
import { NavbarComponent } from './010_header/navbar/navbar.component';
import { HeaderComponent } from './010_header/header/header.component';
import { FooterComponent } from './010_header/footer/footer.component';
import { LibrariesComponent } from './020_find/widget/libraries/libraries.component';
import { LocalStorageService } from './000_core/service/local-storage.service';
import { routes } from './app.routes';
import { Meta, Title } from '@angular/platform-browser';

@Component({
  selector: 'app-root',
  standalone: true,
  imports: [
    RouterOutlet,
    NavbarComponent,
    HeaderComponent,
    FooterComponent,
  ],
  schemas: [CUSTOM_ELEMENTS_SCHEMA],
  templateUrl: './app.component.html',
  styleUrl: './app.component.scss',
})
export class AppComponent {
  title = 'findAPP';
  public libraryID: string = '';

  constructor(
    private titleService:
    Title, private metaService: Meta,
    private findService: FindService, // private router: Router
    private localStorage: LocalStorageService // private localStorage: LocalStorageService
  ) {}

  ngOnInit(): void {
    this.titleService.setTitle('Find Livros');
    this.libraryID = localStorage.getItem('library') || '';
    if (this.libraryID == '') {
      [routes[0].path] = ['selectLibrary'];
    }
  }
}
