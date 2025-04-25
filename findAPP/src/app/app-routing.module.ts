import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { VitrineComponent } from './020_find/page/vitrine/vitrine.component';
import { SelectLibraryComponent } from './020_find/page/select-library/select-library.component';

const routes: Routes = [
  { path: 'selectLibrary', component: SelectLibraryComponent },
//  { path: 'catalog', component: CatalogComponent },
  {
    path: '', component: VitrineComponent, children: [
      { path: 'home', component: VitrineComponent }
    ],
  },
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule],
})
export class AppRoutingModule {}
