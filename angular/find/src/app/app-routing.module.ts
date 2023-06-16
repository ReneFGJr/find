import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { IndexComponent } from './admin/index/index.component';
import { HomeIndexComponent } from './pages/home-index/home-index.component';
import { Error404Component} from './pages/error404/error404.component';
import { BookShowComponent } from './base/book-show/book-show.component';
import { LibraryAdminComponent } from './admin/library-admin/library-admin.component';
import { SocialsComponent } from './socials/socials.component';

const routes: Routes = [
  { path: '', component: HomeIndexComponent },
  { path: 'admin', component: IndexComponent },
  { path: 'viewid', component: BookShowComponent },
  { path: 'admin/library', component: LibraryAdminComponent },
  { path: 'social', component: SocialsComponent },
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
