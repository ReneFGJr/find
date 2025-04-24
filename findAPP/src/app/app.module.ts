import { NgModule } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';

import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { VitrineComponent } from './020_find/widget/vitrine/vitrine.component';
import { WaitingComponent } from './020_find/widget/waiting/waiting.component';
import { SpashPageComponent } from './010_header/spash-page/spash-page.component';

@NgModule({
  declarations: [AppComponent],
  imports: [
    BrowserModule,
    AppRoutingModule,
    VitrineComponent,
    SpashPageComponent,
    WaitingComponent,
  ],
  providers: [

  ],
  bootstrap: [AppComponent],
})
export class AppModule {}
