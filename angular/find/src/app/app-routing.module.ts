import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { WelcomeComponent } from './000_core/page/welcome/welcome.component';
import { LibrariesComponent } from './000_core/page/libraries/libraries.component';
import { MainFindComponent } from './000_core/page/main/main.component';
import { BookComponent } from './000_core/page/book/book.component';
import { MainAdminComponent } from './010_admin/page/main/main.component';
import { IsbnAddComponent } from './010_admin/page/isbn-add/isbn-add.component';

const routes: Routes = [
  {
    path: '', component: MainFindComponent, children:
      [
        { path: '', component: WelcomeComponent },
        { path: 'libraries', component: LibrariesComponent },
        { path: 'libraries/:id', component: LibrariesComponent },
        { path: 'book/:id', component: BookComponent },
        { path: 'admin', component: MainAdminComponent },
        { path: 'admin/isbn/add', component: IsbnAddComponent },
      ]
  }]

@NgModule({
    imports: [RouterModule.forRoot(routes)],
    exports: [RouterModule]
  })
  export class AppRoutingModule { }
