import { NgModule, Component } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { IndexComponent } from './admin/index/index.component';
import { HomeIndexComponent } from './pages/home-index/home-index.component';
import { Error404Component} from './pages/error404/error404.component';
import { BookShowComponent } from './base/book-show/book-show.component';
import { LibraryAdminComponent } from './admin/library-admin/library-admin.component';
import { SocialsComponent } from './socials/socials.component';
import { BooksInsertIsbnComponent } from './form/books/books-insert-isbn/books-insert-isbn.component';
import { SinginComponent } from './socials/singin/singin.component';
import { authGuard } from './socials/auth.guard';
import { SingupComponent } from './socials/singup/singup.component';

const routes: Routes = [
  { path: 'admin', component: IndexComponent,
    children:
    [
        { path: 'library', component: LibraryAdminComponent },
        { path: 'add', component: BooksInsertIsbnComponent },
        { path: '**', redirectTo: 'signIn', pathMatch: 'full' },
    ],
    canActivate: [authGuard]
  },
  { path: '', component: HomeIndexComponent },
  { path: 'social', component: SocialsComponent,
    children:
      [
        { path: '', component: SinginComponent },
        { path: 'signup', component: SingupComponent },
      ]
  } ,

  /*
  { path: '', component: HomeIndexComponent },
  { path: 'viewid', component: BookShowComponent },
  { path: 'viewid', component: BookShowComponent },

  { path: 'social', component: SocialsComponent },
  */
  { path: '**', component: Error404Component },
  ];

@NgModule({
  declarations: [],
  exports: [ RouterModule ],
  imports: [
    RouterModule.forRoot(routes)
  ]
})
export class AppRoutingModule {
}
