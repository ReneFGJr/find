import { Routes } from '@angular/router';
import { VitrineComponent } from './020_find/widget/vitrine/vitrine.component';
import { SelectLibraryComponent } from './020_find/page/select-library/select-library.component';
import { CatalogComponent } from './020_find/widget/catalog/catalog.component';
import { MainAdminComponent } from './030_admin/main-admin/main-admin.component';

export const routes: Routes = [
  { path: 'selectLibrary', component: SelectLibraryComponent },
  { path: 'catalog', component: CatalogComponent },
  {
    path: '',
    component: VitrineComponent,
    children: [{ path: '', component: VitrineComponent }],
  },
  { path: 'admin', component: MainAdminComponent },
];
