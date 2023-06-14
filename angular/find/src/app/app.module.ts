import { NgModule } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';


import { AppComponent } from './app.component';
import { NgbModule } from '@ng-bootstrap/ng-bootstrap';
import { HeadComponent } from './headers/head/head.component';
import { FootComponent } from './headers/foot/foot.component';
import { NavbarComponent } from './headers/navbar/navbar.component';
import { BookItemComponent } from './base/book-item/book-item.component';
import { BookShowComponent } from './base/book-show/book-show.component';
import { ColorComponent } from './base/classification/color/color.component';
import { CddComponent } from './base/classification/cdd/cdd.component';
import { CduComponent } from './base/classification/cdu/cdu.component';
import { AppRoutingModule } from './app-routing.module';
import { IndexComponent } from './admin/index/index.component';
import { Error404Component } from './pages/error404/error404.component';
import { HomeIndexComponent } from './pages/home-index/home-index.component';
import { AdminMenuComponent } from './admin/admin-menu/admin-menu.component';
import { SocialsMenuComponent } from './socials/socials-menu/socials-menu.component';
import { ReactiveFormsModule } from '@angular/forms';
import { BooksInsertIsbnComponent } from './form/books/books-insert-isbn/books-insert-isbn.component';
import { HttpClientModule } from '@angular/common/http';
import { IsbnComponent } from './data/isbn/isbn.component';
import { IsbnApiService } from './service/Api/isbn-api.service';
import { LibraryAdminComponent } from './admin/library-admin/library-admin.component';
import { CookieService } from 'ngx-cookie-service';

@NgModule({
  declarations: [
    AppComponent,
    HeadComponent,
    FootComponent,
    NavbarComponent,
    BookItemComponent,
    BookShowComponent,
    ColorComponent,
    CddComponent,
    CduComponent,
    IndexComponent,
    Error404Component,
    HomeIndexComponent,
    AdminMenuComponent,
    SocialsMenuComponent,
    BooksInsertIsbnComponent,
    IsbnComponent,
    LibraryAdminComponent,
  ],
  imports: [
    BrowserModule,
    NgbModule,
    AppRoutingModule,
    ReactiveFormsModule,
    HttpClientModule,
  ],
  providers: [HttpClientModule, IsbnApiService, CookieService],
  bootstrap: [AppComponent]
})
export class AppModule { }
