import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { VitrineComponent } from './020_find/page/vitrine/vitrine.component';
import { SelectLibraryComponent } from './020_find/page/select-library/select-library.component';
import { TomboComponent } from './040_tombo/page/tombo/tombo.component';
import { ReportsComponent } from './020_find/page/reports/reports.component';
import { LabelTomboComponent } from './040_tombo/page/label-tombo/label-tombo.component';
import { UsersComponent } from './050_users/page/users/users.component';
import { UserAuthComponent } from './050_users/widget/user-auth/user-auth.component';
import { GroupsComponent } from './050_users/page/groups/groups.component';
import { CatalogComponent } from './060_catalog/page/catalog/catalog.component';
import { CatalogInputFormComponent } from './060_catalog/widget/catalog-input-form/catalog-input-form.component';
import { LibrariesComponent } from './020_find/widget/libraries/libraries.component';
import { LibrariesAdminComponent } from './070_libraries/page/libraries-admin/libraries-admin.component';
import { ConfiguraLibraryComponent } from './070_libraries/page/configura-library/configura-library.component';
import { CatalogSearchComponent } from './060_catalog/widget/catalog-search/catalog-search.component';
import { CatalogStatusComponent } from './060_catalog/widget/catalog-status/catalog-status.component';
import { CatalogStatusRowComponent } from './060_catalog/page/catalog-status-row/catalog-status-row.component';
import { CatalogEditRdfComponent } from './060_catalog/widget/catalog-edit-rdf/catalog-edit-rdf.component';

const routes: Routes = [
  //  { path: 'catalog', component: CatalogComponent },
  {
    path: '',
    children: [
      { path: '', component: VitrineComponent },
      { path: 'home', component: SelectLibraryComponent },
      { path: 'tombo', component: TomboComponent },

      { path: 'catalog', component: CatalogInputFormComponent },
      { path: 'libraries/configure/:id', component: ConfiguraLibraryComponent },
      { path: 'libraries/:act', component: LibrariesAdminComponent },
      { path: 'libraries/:act/:id', component: LibrariesAdminComponent },

      { path: 'search', component: CatalogSearchComponent },
      { path: 'catalog/rdf/:id', component: CatalogEditRdfComponent },
      { path: 'catalog/marc/:id', component: CatalogEditRdfComponent },
      { path: 'catalog/status/:id', component: CatalogStatusComponent },
      { path: 'catalog/statusID/:id', component: CatalogStatusRowComponent },
      { path: 'catalog/:act', component: CatalogComponent },
      { path: 'catalog/:act/:id', component: CatalogComponent },

      { path: 'tombo/:act', component: TomboComponent },
      { path: 'label', component: LabelTomboComponent },
      { path: 'selectLibrary', component: SelectLibraryComponent },
      { path: 'report', component: ReportsComponent },
      { path: 'report/:act', component: ReportsComponent },
      { path: 'auth', component: UserAuthComponent },
      { path: 'users', component: UsersComponent },
      { path: 'users/:act', component: UsersComponent },
      { path: 'users/:act/:id', component: UsersComponent },
      { path: 'groups/:act', component: GroupsComponent },
      { path: 'groups/:act/:id', component: GroupsComponent },
    ],
  },
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule],
})
export class AppRoutingModule {}
