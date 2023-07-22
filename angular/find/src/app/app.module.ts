import { NgModule } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';

import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { NgbModule } from '@ng-bootstrap/ng-bootstrap';
import { BrowserAnimationsModule } from '@angular/platform-browser/animations';
import { HeadComponent } from './000_core/header/head/head.component';
import { FootComponent } from './000_core/header/foot/foot.component';
import { NavbarComponent } from './000_core/header/navbar/navbar.component';
import { WelcomeComponent } from './000_core/page/welcome/welcome.component';
import { ActivatedRoute, Router, RouterModule } from '@angular/router';
import { LibrariesComponent } from './000_core/page/libraries/libraries.component';
import { MainFindComponent } from './000_core/page/main/main.component';
import { HttpClient, HttpClientModule } from '@angular/common/http';
import { VitrineComponent } from './000_core/page/vitrine/vitrine.component';
import { BookComponent } from './000_core/page/book/book.component';

@NgModule({
  declarations: [
    AppComponent,
    HeadComponent,
    FootComponent,
    NavbarComponent,
    WelcomeComponent,
    LibrariesComponent,
    MainFindComponent,
    VitrineComponent,
    BookComponent,
    ],
  imports: [
    BrowserModule,
    AppRoutingModule,
    NgbModule,
    BrowserAnimationsModule,
    RouterModule,
    HttpClientModule
  ],
  providers: [HttpClient],
  bootstrap: [AppComponent]
})
export class AppModule { }
