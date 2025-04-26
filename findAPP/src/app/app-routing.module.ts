import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { VitrineComponent } from './020_find/page/vitrine/vitrine.component';
import { SelectLibraryComponent } from './020_find/page/select-library/select-library.component';
import { TomboComponent } from './040_tombo/page/tombo/tombo.component';

const routes: Routes = [
  //  { path: 'catalog', component: CatalogComponent },
  {
    path: '',
    children: [
      { path: '', component: VitrineComponent },
      { path: 'home', component: SelectLibraryComponent },
      { path: 'tombo', component: TomboComponent },
      { path: 'selectLibrary', component: SelectLibraryComponent },
    ],
  },
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule],
})
export class AppRoutingModule {}
