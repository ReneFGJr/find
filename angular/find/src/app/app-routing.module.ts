import { CommonModule } from '@angular/common';
import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { WelcomeComponent } from './000_core/page/welcome/welcome.component';
import { LibrariesComponent } from './000_core/page/libraries/libraries.component';
import { MainFindComponent } from './000_core/page/main/main.component';
import { BookComponent } from './000_core/page/book/book.component';
import { MainAdminComponent } from './010_admin/page/main/main.component';
import { IsbnAddComponent } from './010_admin/page/isbn-add/isbn-add.component';
import { BookSearchComponent } from './010_admin/page/book-search/book-search.component';
import { SelectLibaryComponent } from './030_library/page/select/select.component';
import { VComponent } from './000_core/page/v/v.component';
import { IndexComponent } from './040_index/page/index/index.component';
import { LibraryComponent } from './010_admin/page/library/library.component';
import { AdminComponent } from './010_admin/page/admin/admin.component';
import { HomeAdminComponent } from './010_admin/page/home/home.component';
import { LibraryEditComponent } from './010_admin/page/library-edit/library-edit.component';
import { LoginComponent } from './050_login/login/login.component';
import { VitrineV2Component } from './060_vitrine/page/vitrine-v2/vitrine-v2.component';
import { SigninComponent } from './050_login/signin/signin.component';
import { ForgotComponent } from './050_login/forgot/forgot.component';

const routes: Routes = [
  {
    path: '',
    component: MainFindComponent,
    children: [
      { path: '', component: WelcomeComponent },
      {
        path: 'admin',
        component: AdminComponent,
        children: [
          { path: 'libraries', component: LibraryComponent },
          { path: 'library/:id', component: LibraryEditComponent },
          { path: 'home', component: HomeAdminComponent },
        ],
      },
      { path: 'library', component: SelectLibaryComponent },
      { path: 'libraries', component: LibrariesComponent },
      { path: 'libraries/:id', component: LibrariesComponent },
      { path: 'book/:id/:lib', component: BookComponent },
      { path: 'admin', component: MainAdminComponent },
      { path: 'admin/isbn/add', component: IsbnAddComponent },
      { path: 'admin/isbn/search', component: BookSearchComponent },
      { path: 'v/:id', component: VComponent },
      { path: 'index/:type', component: IndexComponent },

      { path: 'login', component: LoginComponent },
      { path: 'signup', component: SigninComponent },
      { path: 'forgout', component: ForgotComponent },

      { path: 'vitrine', component: VitrineV2Component },
    ],
  },
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule],
})
export class AppRoutingModule {}
