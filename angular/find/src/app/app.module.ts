import { NgModule } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';


import { AppComponent } from './app.component';
import { NgbModule } from '@ng-bootstrap/ng-bootstrap';
import { HeadComponent } from './headers/head/head.component';
import { FootComponent } from './headers/foot/foot.component';
import { NavbarComponent } from './headers/navbar/navbar.component';
import { BookItemComponent } from './base/book-item/book-item.component';
import { BookShowComponent } from './base/book-show/book-show.component';
import { ColorComponent } from './base/classification/color/color.component';
import { CddComponent } from './base/classification/cdd/cdd.component';
import { CduComponent } from './base/classification/cdu/cdu.component';

@NgModule({
  declarations: [
    AppComponent,
    HeadComponent,
    FootComponent,
    NavbarComponent,
    BookItemComponent,
    BookShowComponent,
    ColorComponent,
    CddComponent,
    CduComponent
  ],
  imports: [
    BrowserModule,
    NgbModule
  ],
  providers: [],
  bootstrap: [AppComponent]
})
export class AppModule { }
