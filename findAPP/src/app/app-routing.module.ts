import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { VitrineComponent } from './020_find/widget/vitrine/vitrine.component';

const routes: Routes = [
  {
    path: '',
    component: VitrineComponent,
    children: [{ path: '', component: VitrineComponent }],
  },
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule { }
