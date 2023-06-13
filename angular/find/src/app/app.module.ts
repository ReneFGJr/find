import { NgModule } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';


import { AppComponent } from './app.component';
import { NgbModule } from '@ng-bootstrap/ng-bootstrap';
import { HeadComponent } from './headers/head/head.component';
import { FootComponent } from './headers/foot/foot.component';
import { NavbarComponent } from './headers/navbar/navbar.component';

@NgModule({
  declarations: [
    AppComponent,
    HeadComponent,
    FootComponent,
    NavbarComponent
  ],
  imports: [
    BrowserModule,
    NgbModule
  ],
  providers: [],
  bootstrap: [AppComponent]
})
export class AppModule { }
