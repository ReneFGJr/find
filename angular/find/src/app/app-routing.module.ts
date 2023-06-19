import { NgModule, Component } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { IndexComponent } from './admin/index/index.component';
import { HomeIndexComponent } from './pages/home-index/home-index.component';
import { Error404Component } from './pages/error404/error404.component';
import { BookShowComponent } from './base/book-show/book-show.component';
import { SocialsComponent } from './socials/socials.component';
import { BooksInsertIsbnComponent } from './form/books/books-insert-isbn/books-insert-isbn.component';
import { SinginComponent } from './socials/singin/singin.component';
import { libraryGuard } from './find/library.guard';
import { SingupComponent } from './socials/singup/singup.component';
import { LibraryComponent } from './find/library/library.component';
import { SelectComponent } from './find/library/select/select.component';
import { SelectedComponent } from './find/library/selected/selected.component';

const routes: Routes = [
  { path: 'library', component: SelectComponent },
  { path: 'library/:id', component: SelectedComponent},

  /****************************************** LOGIN */
  {
    path: 'social', component: SocialsComponent,
    children:
      [
        { path: '', component: SinginComponent },
        { path: 'signup', component: SingupComponent },
      ]
  },


  /*************************************************** HOME */
  {
    path: 'main', component: HomeIndexComponent,
    children:
      [
        { path: '', component: HomeIndexComponent },
        { path: 'viewid', component: BookShowComponent },
        { path: 'viewid', component: BookShowComponent },
      ],
    canActivate: [libraryGuard]
  },

  {
    path: "",
    redirectTo: "/library",
    pathMatch: "full",
  },
];

const rela =[
  /****************************************** PAGINA INICIAL */
  { path: '', component: LibraryComponent, canActivate: [libraryGuard] },
  { path: '', redirectTo: "/library", pathMatch: 'full' },
  { path: 'library', component: SelectComponent },
  {
    /****************************************** ADMIN */
    path: 'admin', component: IndexComponent,
    children:
      [
        { path: 'library', component: SelectComponent },
        { path: 'add', component: BooksInsertIsbnComponent },
        { path: '**', redirectTo: 'signIn', pathMatch: 'full' },
      ],
  },
  /****************************************** BIBLIOTECA */
  {
    path: 'library', component: HomeIndexComponent,
    children:
      [
        { path: '', component: HomeIndexComponent },
        { path: 'viewid', component: BookShowComponent },
        { path: 'viewid', component: BookShowComponent },
      ],
    canActivate: [libraryGuard]
  },


  /****************************************** ERRO DE PAGINA */
  { path: '**', component: Error404Component },
];

@NgModule({
  declarations: [],
  exports: [RouterModule],
  imports: [
    RouterModule.forRoot(routes)
  ]
})
export class AppRoutingModule {
}
