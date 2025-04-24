import { NgModule } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';

import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { VitrineComponent } from './020_find/widget/vitrine/vitrine.component';
import { WaitingComponent } from './020_find/widget/waiting/waiting.component';
import { SpashPageComponent } from './010_header/spash-page/spash-page.component';
import { BookComponent } from './020_find/widget/book/book.component';
import { NgbModule } from '@ng-bootstrap/ng-bootstrap';

@NgModule({
  declarations: [
    AppComponent,
    VitrineComponent,
    SpashPageComponent,
    WaitingComponent,
    BookComponent,
  ],
  imports: [BrowserModule, AppRoutingModule, NgbModule],
  providers: [],
  bootstrap: [AppComponent],
})
export class AppModule {}
