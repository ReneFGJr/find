import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { VitrineComponent } from './020_find/page/vitrine/vitrine.component';
import { SelectLibraryComponent } from './020_find/page/select-library/select-library.component';
import { TomboComponent } from './040_tombo/page/tombo/tombo.component';
import { ReportsComponent } from './020_find/page/reports/reports.component';
import { LabelTomboComponent } from './040_tombo/page/label-tombo/label-tombo.component';

const routes: Routes = [
  //  { path: 'catalog', component: CatalogComponent },
  {
    path: '',
    children: [
      { path: '', component: VitrineComponent },
      { path: 'home', component: SelectLibraryComponent },
      { path: 'tombo', component: TomboComponent },
      { path: 'label', component: LabelTomboComponent },
      { path: 'selectLibrary', component: SelectLibraryComponent },
      { path: 'report/:act', component: ReportsComponent },
    ],
  },
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule],
})
export class AppRoutingModule {}
