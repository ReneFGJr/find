import { Routes } from '@angular/router';
import { VitrineComponent } from './020_find/widget/vitrine/vitrine.component';
import { SelectLibraryComponent } from './020_find/page/select-library/select-library.component';

export const routes: Routes = [
  { path: 'selectLibrary', component: SelectLibraryComponent },
  {
    path: '',
    component: VitrineComponent,
    children: [{ path: '', component: VitrineComponent }],
  },
];
