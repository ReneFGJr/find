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

const routes: Routes = [
  {
    path: '',
    component: MainFindComponent,
    children: [
      { path: '', component: WelcomeComponent },
      { path: 'libraries', component: LibrariesComponent },
      { path: 'libraries/:id', component: LibrariesComponent },
      { path: 'book/:id/:lib', component: BookComponent },
      { path: 'admin', component: MainAdminComponent },
      { path: 'admin/isbn/add', component: IsbnAddComponent },
      { path: 'admin/isbn/search', component: BookSearchComponent },
      { path: 'library', component: SelectLibaryComponent },
      { path: 'v/:id', component: VComponent },
    ],
  },
];

@NgModule({
    imports: [RouterModule.forRoot(routes)],
    exports: [RouterModule]
  })
  export class AppRoutingModule { }
