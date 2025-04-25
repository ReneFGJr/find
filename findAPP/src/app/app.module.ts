import { CUSTOM_ELEMENTS_SCHEMA, NgModule } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';

import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { NgbModule } from '@ng-bootstrap/ng-bootstrap';
import { VitrineComponent } from './020_find/page/vitrine/vitrine.component';
import { FooterComponent } from './010_header/footer/footer.component';
import { HeaderComponent } from './010_header/header/header.component';
import { NavbarComponent } from './010_header/navbar/navbar.component';
import { SpashPageComponent } from './010_header/spash-page/spash-page.component';
import { BookViewComponent } from './020_find/widget/book-view/book-view.component';
import { SelectLibraryComponent } from './020_find/page/select-library/select-library.component';
import { HttpClientModule } from '@angular/common/http';
import { BookComponent } from './020_find/widget/book/book.component';
import { CatalogComponent } from './020_find/widget/catalog/catalog.component';
import { LibrariesComponent } from './020_find/widget/libraries/libraries.component';
import { BrowserAnimationsModule } from '@angular/platform-browser/animations';
import { WaitingComponent } from './020_find/widget/waiting/waiting.component';

@NgModule({
  declarations: [
    AppComponent,
    VitrineComponent,
    FooterComponent,
    HeaderComponent,
    NavbarComponent,
    SpashPageComponent,
    BookViewComponent,
    SelectLibraryComponent,
    BookComponent,
    CatalogComponent,
    LibrariesComponent,
    WaitingComponent,
  ],
  imports: [
    BrowserModule,
    HttpClientModule,
    AppRoutingModule,
    NgbModule,
    BrowserAnimationsModule,

  ],
  schemas: [CUSTOM_ELEMENTS_SCHEMA],
  providers: [],
  bootstrap: [AppComponent],
})
export class AppModule {}
