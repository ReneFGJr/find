import { NgModule } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';
import { RouterModule } from '@angular/router';
import { HttpClient, HttpClientModule } from '@angular/common/http';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { CommonModule } from '@angular/common';


import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { NgbModule } from '@ng-bootstrap/ng-bootstrap';

import { HeadComponent } from './000_core/header/head/head.component';
import { FootComponent } from './000_core/header/foot/foot.component';
import { NavbarComponent } from './000_core/header/navbar/navbar.component';
import { WelcomeComponent } from './000_core/page/welcome/welcome.component';
import { LibrariesComponent } from './000_core/page/libraries/libraries.component';
import { MainFindComponent } from './000_core/page/main/main.component';
import { VitrineComponent } from './000_core/page/vitrine/vitrine.component';
import { BookComponent } from './000_core/page/book/book.component';

import { MainAdminComponent } from './010_admin/page/main/main.component';
import { IsbnAddComponent } from './010_admin/page/isbn-add/isbn-add.component';
import { PreviewComponent } from './010_admin/page/preview/preview.component';
import { ClassificationColorComponent } from './000_core/gadget/classification-color/classification-color.component';
import { ClassificationCddComponent } from './000_core/gadget/classification-cdd/classification-cdd.component';
import { ClassificationCduComponent } from './000_core/gadget/classification-cdu/classification-cdu.component';
import { SubjectComponent } from './000_core/gadget/subject/subject.component';
import { SubjectThesaComponent } from './000_core/gadget/subject-thesa/subject-thesa.component';
import { SubjectSkosComponent } from './000_core/gadget/subject-skos/subject-skos.component';
import { ItemAddIsbnComponent } from './000_core/page/item-add-isbn/item-add-isbn.component';
import { ItemListIsbnComponent } from './000_core/page/item-list-isbn/item-list-isbn.component';

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
    MainAdminComponent,
    IsbnAddComponent,
    PreviewComponent,
    ClassificationColorComponent,
    ClassificationCddComponent,
    ClassificationCduComponent,
    SubjectComponent,
    SubjectThesaComponent,
    SubjectSkosComponent,
    ItemAddIsbnComponent,
    ItemListIsbnComponent,
    ],
  imports: [
    BrowserModule,
    AppRoutingModule,
    NgbModule,
    RouterModule,
    HttpClientModule,
    ReactiveFormsModule,
    FormsModule
  ],
  exports: [
    CommonModule,
    FormsModule,
    ReactiveFormsModule,
    RouterModule
  ],
  providers: [HttpClient],
  bootstrap: [AppComponent]
})
export class AppModule { }
