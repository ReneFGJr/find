import { NgModule } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';
import { RouterModule } from '@angular/router';
import { HttpClient, HttpClientModule } from '@angular/common/http';
import { FormControlDirective, FormGroupDirective, FormsModule, ReactiveFormsModule } from '@angular/forms';
import { CommonModule, HashLocationStrategy, LocationStrategy } from '@angular/common';


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
import { UploadImageComponent } from './000_core/gadget/upload-image/upload-image.component';
import { SearchComponent } from './020_search/page/search/search.component';
import { FieldsComponent } from './010_admin/page/fields/fields.component';
import { SearchGoogleComponent } from './000_core/gadget/google/search/search.component';
import { BrowserAnimationsModule } from '@angular/platform-browser/animations';
import { BookSearchComponent } from './010_admin/page/book-search/book-search.component';
import { SelectLibaryComponent } from './030_library/page/select/select.component';
import { ItemPlacesComponent } from './000_core/widgat/item-places/item-places.component';
import { VComponent } from './000_core/page/v/v.component';
import { IndexComponent } from './040_index/page/index/index.component';
import { LibraryComponent } from './010_admin/page/library/library.component';
import { AdminComponent } from './010_admin/page/admin/admin.component';
import { SidebarComponent } from './000_core/header/sidebar/sidebar.component';
import { HomeAdminComponent } from './010_admin/page/home/home.component';
import { LibraryEditComponent } from './010_admin/page/library-edit/library-edit.component';
import { LoginComponent } from './050_login/login/login.component';
import { VitrineV2Component } from './060_vitrine/page/vitrine-v2/vitrine-v2.component';
import { BookV2Component } from './060_vitrine/widgat/book-v2/book-v2.component';
import { SigninComponent } from './050_login/signin/signin.component';
import { PerfilComponent } from './050_login/perfil/perfil.component';
import { ForgotComponent } from './050_login/forgot/forgot.component';
import { PasswordComponent } from './050_login/password/password.component';
import { LogoutComponent } from './050_login/logout/logout.component';
import { ColorsComponent } from './070_classification/colors/colors.component';
import { CddComponent } from './070_classification/cdd/cdd.component';
import { CduComponent } from './070_classification/cdu/cdu.component';
import { AcervoComponent } from './100_management/acervo/acervo.component';
import { PainelComponent } from './100_management/painel/painel.component';

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
    UploadImageComponent,
    SearchComponent,
    FieldsComponent,
    SearchGoogleComponent,
    BookSearchComponent,
    SelectLibaryComponent,
    ItemPlacesComponent,
    VComponent,
    IndexComponent,
    LibraryComponent,
    AdminComponent,
    SidebarComponent,
    HomeAdminComponent,
    LibraryEditComponent,
    LoginComponent,
    VitrineV2Component,
    BookV2Component,
    SigninComponent,
    PerfilComponent,
    ForgotComponent,
    PasswordComponent,
    LogoutComponent,
    ColorsComponent,
    CddComponent,
    CduComponent,
    AcervoComponent,
    PainelComponent,
  ],
  imports: [
    BrowserModule,
    AppRoutingModule,
    NgbModule,
    RouterModule,
    HttpClientModule,
    ReactiveFormsModule,
    FormsModule,
    BrowserAnimationsModule,
  ],
  exports: [CommonModule, FormsModule, ReactiveFormsModule, RouterModule],
  providers: [
    HttpClient,
    FormControlDirective,
    FormGroupDirective,
    { provide: LocationStrategy, useClass: HashLocationStrategy },
  ],
  bootstrap: [AppComponent],
})
export class AppModule {}
