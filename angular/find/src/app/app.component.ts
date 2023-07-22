import { Component } from '@angular/core';
import { Router } from '@angular/router';

@Component({
  selector: 'app-root',
  template: '<app-head></app-head><app-navbar></app-navbar><router-outlet></router-outlet><app-foot></app-foot>'
})
export class AppComponent {

  constructor(public route: Router) {}
  title = 'find';
}
